<?php

namespace App\Ai\Tools;

use App\Models\EmployeeResignation;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchResignations extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search employee resignation processes in HRWork by employee, status, and date. Includes clearance and handover progress counts. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-resignation')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $employeeId = $request['employee_id'] ?? null;
        $status = $this->value($request['status'] ?? '');
        $activeOnly = filter_var($request['active_only'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 50);

        $resignations = EmployeeResignation::query()
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
            ->when($activeOnly, fn ($q) => $q->whereIn('status', [
                EmployeeResignation::STATUS_SUBMITTED,
                EmployeeResignation::STATUS_APPROVED,
            ]))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($from, fn ($q) => $q->whereDate('proposed_last_working_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('proposed_last_working_date', '<=', $to))
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return $this->json([
            'success' => true,
            'count' => $resignations->count(),
            'resignations' => $resignations->map(fn (EmployeeResignation $resignation) => [
                'id' => $resignation->id,
                'employee_id' => $resignation->employee_id,
                'employee_code' => $resignation->employee?->employee_code,
                'employee_name' => $resignation->employee?->user?->name,
                'position' => $resignation->employee?->position?->name,
                'division' => $resignation->employee?->team?->divisi?->name,
                'status' => $resignation->status,
                'submitted_at' => $resignation->submitted_at?->toDateTimeString(),
                'proposed_last_working_date' => $resignation->proposed_last_working_date?->toDateString(),
                'approved_last_working_date' => $resignation->approved_last_working_date?->toDateString(),
                'reason' => $resignation->reason,
                'notes' => $resignation->notes,
                'pending_clearances' => (int) $resignation->pending_clearances_count,
                'completed_clearances' => (int) $resignation->completed_clearances_count,
                'pending_handover' => (int) $resignation->pending_handover_count,
                'completed_handover' => (int) $resignation->completed_handover_count,
                'exit_interview_at' => $resignation->exit_interview_at?->toDateTimeString(),
            ])->values()->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'query' => $schema->string(),
            'status' => $schema->string(),
            'active_only' => $schema->boolean(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
