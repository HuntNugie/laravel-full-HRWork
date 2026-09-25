<?php

namespace App\Ai\Tools;

use App\Models\Attendances;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchAttendance extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search attendance records in HRWork by employee, date range, status, or lateness. Returns check-in, check-out, work duration, late minutes, status, and notes. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-monitor-attendance')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $employeeId = $request['employee_id'] ?? null;
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $status = $this->value($request['status'] ?? '');
        $lateOnly = filter_var($request['late_only'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 50);

        $rows = Attendances::query()
            ->with(['employees.user:id,name,email'])
            ->when($employeeId, fn ($q) => $q->where('employee_id', (int) $employeeId))
            ->when($query !== '', function ($q) use ($query) {
                $q->whereHas('employees', function ($employeeQuery) use ($query) {
                    $employeeQuery->where('employee_code', 'like', "%{$query}%")
                        ->orWhereHas('user', function ($userQuery) use ($query) {
                            $userQuery
                                ->where('name', 'like', "%{$query}%")
                                ->orWhere('email', 'like', "%{$query}%");
                        });
                });
            })
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($lateOnly, fn ($q) => $q->where('late_minutes', '>', 0))
            ->orderByDesc('date')
            ->limit($limit)
            ->get();

        $lateDays = $rows->where('late_minutes', '>', 0)->count();
        $checkInDays = $rows->whereNotNull('check_in_at')->count();

        return $this->json([
            'success' => true,
            'count' => $rows->count(),
            'summary' => [
                'records_returned' => $rows->count(),
                'days_with_check_in' => $checkInDays,
                'late_days' => $lateDays,
                'total_late_minutes' => $rows->sum('late_minutes'),
            ],
            'attendances' => $rows->map(fn (Attendances $attendance) => [
                'employee_id' => $attendance->employee_id,
                'employee_code' => $attendance->employees?->employee_code,
                'employee_name' => $attendance->employees?->user?->name,
                'date' => $attendance->date?->toDateString(),
                'check_in_at' => $attendance->check_in_at?->toDateTimeString(),
                'check_out_at' => $attendance->check_out_at?->toDateTimeString(),
                'status' => $attendance->status,
                'late_minutes' => (int) $attendance->late_minutes,
                'work_duration_minutes' => $attendance->work_duration !== null ? (int) $attendance->work_duration : null,
                'notes' => $attendance->notes,
            ])->values()->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'query' => $schema->string(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'status' => $schema->string(),
            'late_only' => $schema->boolean(),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
