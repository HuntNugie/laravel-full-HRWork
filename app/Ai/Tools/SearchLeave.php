<?php

namespace App\Ai\Tools;

use App\Models\EmployeeAbsenceRequest;
use App\Models\LeaveRequest;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchLeave extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search leave and sickness/permit absence records in HRWork. Can filter employee, type, status, and date range. When requested, also calculates the current leave entitlement and approved leave usage for the employee.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-management-leave')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $employeeId = $request['employee_id'] ?? null;
        $kind = $this->value($request['kind'] ?? 'all');
        $status = $this->value($request['status'] ?? '');
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $includeBalance = filter_var($request['include_balance'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 50);

        $leaveRequests = collect();
        $absenceRequests = collect();

        if ($kind !== 'absence') {
            $leaveRequests = LeaveRequest::query()
                ->with(['employees.user:id,name,email', 'leaveType:id,name', 'employeeContract:id,contract_numnber'])
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
                ->when($status !== '', fn ($q) => $q->where('status', $status))
                ->when($from, fn ($q) => $q->whereDate('end_date', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('start_date', '<=', $to))
                ->latest('created_at')
                ->limit($limit)
                ->get();
        }

        if ($kind !== 'leave') {
            $absenceRequests = EmployeeAbsenceRequest::query()
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
                ->when($status !== '', fn ($q) => $q->where('status', $status))
                ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
                ->latest('created_at')
                ->limit($limit)
                ->get();
        }

        $result = [
            'success' => true,
            'leave_requests' => $leaveRequests->map(fn (LeaveRequest $item) => [
                'employee_id' => $item->employee_id,
                'employee_code' => $item->employees?->employee_code,
                'employee_name' => $item->employees?->user?->name,
                'leave_type' => $item->leaveType?->name,
                'start_date' => $item->start_date?->toDateString(),
                'end_date' => $item->end_date?->toDateString(),
                'total_days' => (int) $item->total_days,
                'status' => $item->status,
                'reason' => $item->reason,
                'contract_number' => $item->employeeContract?->contract_numnber,
            ])->values()->all(),
            'absence_requests' => $absenceRequests->map(fn (EmployeeAbsenceRequest $item) => [
                'employee_id' => $item->employee_id,
                'employee_code' => $item->employees?->employee_code,
                'employee_name' => $item->employees?->user?->name,
                'date' => $item->date?->toDateString(),
                'type' => $item->type,
                'status' => $item->status,
                'reason' => $item->reason,
            ])->values()->all(),
        ];

        if ($includeBalance && $employeeId) {
            $employee = \App\Models\Employees::query()
                ->with(['latestEmployeeContract.contractLeave.leaveType'])
                ->find((int) $employeeId);

            if ($employee?->latestEmployeeContract) {
                $contract = $employee->latestEmployeeContract;
                $approvedUsage = $contract->leaveRequest()
                    ->where('status', 'approved')
                    ->sum('total_days');

                $result['leave_balance'] = [
                    'employee_id' => $employee->id,
                    'contract_number' => $contract->contract_numnber,
                    'entitlements' => $contract->contractLeave->map(fn ($entitlement) => [
                        'leave_type' => $entitlement->leaveType?->name,
                        'entitled_days' => (int) $entitlement->days,
                        'approved_used_days' => (int) $contract->leaveRequest()
                            ->where('status', 'approved')
                            ->where('leave_type_id', $entitlement->leave_type_id)
                            ->sum('total_days'),
                    ])->values()->map(fn ($item) => [
                        ...$item,
                        'remaining_days' => max(
                            0,
                            $item['entitled_days'] - $item['approved_used_days']
                        ),
                    ])->all(),
                ];
            }
        }

        return $this->json($result);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'query' => $schema->string(),
            'kind' => $schema->string(),
            'status' => $schema->string(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'include_balance' => $schema->boolean(),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
