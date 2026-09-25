<?php

namespace App\Ai\Tools;

use App\Models\EmployeeTermination;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchTerminations extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search employee termination processes in HRWork by employee, reason type, status, and effective date. Includes clearance and handover progress counts. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-termination')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $employeeId = $request['employee_id'] ?? null;
        $status = $this->value($request['status'] ?? '');
        $reasonType = $this->value($request['reason_type'] ?? '');
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 50);

        $terminations = EmployeeTermination::query()
            ->with(['employee.user:id,name,email', 'employee.position:id,name', 'employee.team.divisi:id,name'])
            ->withCount([
                'clearances as pending_clearances_count' => fn ($q) => $q->where('status', 'pending'),
                'clearances as completed_clearances_count' => fn ($q) => $q->where('status', 'completed'),
                'handoverItems as pending_handover_count' => fn ($q) => $q->where('status', 'pending'),
                'handoverItems as completed_handover_count' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->when($employeeId, fn ($q) => $q->where('employee_id', (int) $employeeId))
            ->when($query !== '', function ($q) use ($query) {
                $q->whereHas('employee', function ($employeeQuery) use ($query) {
                    $employeeQuery->where('employee_code', 'like', "%{$query}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$query}%"));
                });
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($reasonType !== '', fn ($q) => $q->where('reason_type', $reasonType))
            ->when($from, fn ($q) => $q->whereDate('effective_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('effective_date', '<=', $to))
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return $this->json([
            'success' => true,
            'count' => $terminations->count(),
            'terminations' => $terminations->map(fn (EmployeeTermination $termination) => [
                'id' => $termination->id,
                'employee_id' => $termination->employee_id,
                'employee_code' => $termination->employee?->employee_code,
                'employee_name' => $termination->employee?->user?->name,
                'position' => $termination->employee?->position?->name,
                'division' => $termination->employee?->team?->divisi?->name,
                'status' => $termination->status,
                'effective_date' => $termination->effective_date?->toDateString(),
                'reason_type' => $termination->reason_type,
                'reason' => $termination->reason,
                'notes' => $termination->notes,
                'pending_clearances' => (int) $termination->pending_clearances_count,
                'completed_clearances' => (int) $termination->completed_clearances_count,
                'pending_handover' => (int) $termination->pending_handover_count,
                'completed_handover' => (int) $termination->completed_handover_count,
            ])->values()->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'query' => $schema->string(),
            'status' => $schema->string(),
            'reason_type' => $schema->string(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
