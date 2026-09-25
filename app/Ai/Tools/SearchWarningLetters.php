<?php

namespace App\Ai\Tools;

use App\Models\EmployeeWarningLetter;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchWarningLetters extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search employee warning letters in HRWork by employee, warning level, status, and issue date. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-warning-letter')) {
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
        $level = $this->value($request['level'] ?? '');
        $status = $this->value($request['status'] ?? '');
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 50);

        $letters = EmployeeWarningLetter::query()
            ->with(['employee.user:id,name,email', 'employee.position:id,name'])
            ->when($employeeId, fn ($q) => $q->where('employee_id', (int) $employeeId))
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($builder) use ($query) {
                    $builder
                        ->where('letter_number', 'like', "%{$query}%")
                        ->orWhere('warning_level', 'like', "%{$query}%")
                        ->orWhereHas('employee', function ($employeeQuery) use ($query) {
                            $employeeQuery->where('employee_code', 'like', "%{$query}%")
                                ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$query}%"));
                        });
                });
            })
            ->when($level !== '', fn ($q) => $q->where('warning_level', $level))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($from, fn ($q) => $q->whereDate('issued_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('issued_date', '<=', $to))
            ->orderByDesc('issued_date')
            ->limit($limit)
            ->get();

        return $this->json([
            'success' => true,
            'count' => $letters->count(),
            'warning_letters' => $letters->map(fn (EmployeeWarningLetter $letter) => [
                'id' => $letter->id,
                'employee_id' => $letter->employee_id,
                'employee_code' => $letter->employee?->employee_code,
                'employee_name' => $letter->employee?->user?->name,
                'position' => $letter->employee?->position?->name,
                'warning_level' => $letter->warning_level,
                'letter_number' => $letter->letter_number,
                'issued_date' => $letter->issued_date?->toDateString(),
                'reason' => $letter->reason,
                'description' => $letter->description,
                'status' => $letter->status,
                'cancellation_reason' => $letter->cancellation_reason,
            ])->values()->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'employee_code' => $schema->string(),
            'query' => $schema->string(),
            'level' => $schema->string(),
            'status' => $schema->string(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
