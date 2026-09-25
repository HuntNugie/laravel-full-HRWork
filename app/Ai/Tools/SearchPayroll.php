<?php

namespace App\Ai\Tools;

use App\Models\Payroll;
use App\Models\PayrollPeriod;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchPayroll extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search payroll records and payroll periods in HRWork. Can filter by employee, period, status, and date range. Returns salary snapshot, attendance snapshot, gross/net amounts, payment status, and optional payroll items.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-payroll')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $employeeCode = $this->value($request['employee_code'] ?? '');
        $employeeId = $request['employee_id'] ?? null;
        $resolved = $this->resolveEmployee($employeeId, $employeeCode, $employeeCode === '' ? $query : '');

        if ($resolved['error']) {
            return $this->json([
                'success' => false,
                'message' => $resolved['message'],
            ]);
        }

        $employeeId = $resolved['employee_id'];
        $periodId = $request['period_id'] ?? null;
        $status = $this->value($request['status'] ?? '');
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $includeItems = filter_var($request['include_items'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 50);

        $periods = PayrollPeriod::query()
            ->when($periodId, fn ($q) => $q->whereKey((int) $periodId))
            ->when($query !== '', fn ($q) => $q->where('name', 'like', "%{$query}%"))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($from, fn ($q) => $q->whereDate('end_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('start_date', '<=', $to))
            ->orderByDesc('start_date')
            ->limit($limit)
            ->get();

        $payrolls = Payroll::query()
            ->with([
                'employees.user:id,name,email',
                'period:id,name,start_date,end_date,payment_date,status',
                ...($includeItems ? ['items:id,payroll_id,name,type,category,amount,quantity,rate,source,description'] : []),
            ])
            ->when($employeeId, fn ($q) => $q->where('employee_id', (int) $employeeId))
            ->when($periodId, fn ($q) => $q->where('payroll_period_id', (int) $periodId))
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($builder) use ($query) {
                    $builder
                        ->whereHas('employees', function ($employeeQuery) use ($query) {
                            $employeeQuery->where('employee_code', 'like', "%{$query}%")
                                ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$query}%"));
                        })
                        ->orWhereHas('period', fn ($periodQuery) => $periodQuery->where('name', 'like', "%{$query}%"));
                });
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($from, fn ($q) => $q->whereHas('period', fn ($periodQuery) => $periodQuery->whereDate('end_date', '>=', $from)))
            ->when($to, fn ($q) => $q->whereHas('period', fn ($periodQuery) => $periodQuery->whereDate('start_date', '<=', $to)))
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return $this->json([
            'success' => true,
            'payroll_periods' => $periods->map(fn (PayrollPeriod $period) => [
                'id' => $period->id,
                'name' => $period->name,
                'start_date' => $period->start_date?->toDateString(),
                'end_date' => $period->end_date?->toDateString(),
                'payment_date' => $period->payment_date?->toDateString(),
                'status' => $period->status,
            ])->values()->all(),
            'payrolls' => $payrolls->map(fn (Payroll $payroll) => [
                'id' => $payroll->id,
                'employee_id' => $payroll->employee_id,
                'employee_code' => $payroll->employees?->employee_code,
                'employee_name' => $payroll->employees?->user?->name,
                'period' => $payroll->period ? [
                    'id' => $payroll->period->id,
                    'name' => $payroll->period->name,
                    'start_date' => $payroll->period->start_date?->toDateString(),
                    'end_date' => $payroll->period->end_date?->toDateString(),
                    'status' => $payroll->period->status,
                ] : null,
                'position_name' => $payroll->position_name,
                'salary_daily' => (string) $payroll->salary_daily,
                'working_days' => (int) $payroll->working_days,
                'present_days' => (int) $payroll->present_days,
                'late_days' => (int) $payroll->late_days,
                'absent_days' => (int) $payroll->absent_days,
                'paid_leave_days' => (int) $payroll->paid_leave_days,
                'unpaid_leave_days' => (int) $payroll->unpaid_leave_days,
                'paid_days' => (int) $payroll->paid_days,
                'gross_amount' => (string) $payroll->gross_amount,
                'deduction_amount' => (string) $payroll->deduction_amount,
                'net_amount' => (string) $payroll->net_amount,
                'status' => $payroll->status,
                'processed_at' => $payroll->processed_at?->toDateTimeString(),
                'paid_at' => $payroll->paid_at?->toDateTimeString(),
                'items' => $includeItems
                    ? $payroll->items->map(fn ($item) => [
                        'name' => $item->name,
                        'type' => $item->type,
                        'category' => $item->category,
                        'amount' => (string) $item->amount,
                        'quantity' => $item->quantity !== null ? (string) $item->quantity : null,
                        'rate' => $item->rate !== null ? (string) $item->rate : null,
                        'source' => $item->source,
                        'description' => $item->description,
                    ])->values()->all()
                    : null,
            ])->values()->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'employee_code' => $schema->string(),
            'period_id' => $schema->integer(),
            'query' => $schema->string(),
            'status' => $schema->string(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'include_items' => $schema->boolean(),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
