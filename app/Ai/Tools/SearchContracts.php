<?php

namespace App\Ai\Tools;

use App\Models\EmployeeContract;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchContracts extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search employee contracts in HRWork by employee, contract status, employment type, and date. Can also find contracts expiring within a number of days. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-contract')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $employeeCode = $this->value($request['employee_code'] ?? '');
        $employeeId = $request['employee_id'] ?? null;
        $resolved = $this->resolveEmployee($employeeId, $employeeCode);

        if ($resolved['error']) {
            return $this->json([
                'success' => false,
                'message' => $resolved['message'],
            ]);
        }

        $employeeId = $resolved['employee_id'];
        $status = $this->value($request['status'] ?? '');
        $employmentType = $this->value($request['employment_type'] ?? '');
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $expiringWithin = $request['expiring_within_days'] ?? null;
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 50);

        $contracts = EmployeeContract::query()
            ->with([
                'employees.user:id,name,email',
                'employees.position:id,name',
                'employees.team.divisi:id,name',
            ])
            ->when($employeeId, fn ($q) => $q->where('employee_id', (int) $employeeId))
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($builder) use ($query) {
                    $builder
                        ->where('contract_number', 'like', "%{$query}%")
                        ->orWhereHas('employees', function ($employeeQuery) use ($query) {
                            $employeeQuery->where('employee_code', 'like', "%{$query}%")
                                ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$query}%"));
                        });
                });
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($employmentType !== '', fn ($q) => $q->where('employement_type', $employmentType))
            ->when($from, fn ($q) => $q->whereDate('end_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('start_date', '<=', $to))
            ->when($expiringWithin !== null, function ($q) use ($expiringWithin) {
                $days = min(max((int) $expiringWithin, 0), 3650);
                $q->whereNotNull('end_date')
                    ->whereBetween('end_date', [today(), today()->addDays($days)]);
            })
            ->orderByDesc('start_date')
            ->limit($limit)
            ->get();

        return $this->json([
            'success' => true,
            'count' => $contracts->count(),
            'contracts' => $contracts->map(fn (EmployeeContract $contract) => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'employee_id' => $contract->employee_id,
                'employee_code' => $contract->employees?->employee_code,
                'employee_name' => $contract->employees?->user?->name,
                'position' => $contract->employees?->position?->name,
                'division' => $contract->employees?->team?->divisi?->name,
                'employment_type' => $contract->employement_type,
                'start_date' => $contract->start_date?->toDateString(),
                'end_date' => $contract->end_date?->toDateString(),
                'salary_daily' => (string) $contract->salary_daily,
                'status' => $contract->status,
                'notes' => $contract->notes,
            ])->values()->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'employee_code' => $schema->string(),
            'query' => $schema->string(),
            'status' => $schema->string(),
            'employment_type' => $schema->string(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'expiring_within_days' => $schema->integer()->min(0)->max(3650),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
