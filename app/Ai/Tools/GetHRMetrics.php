<?php

namespace App\Ai\Tools;

use App\Models\Attendances;
use App\Models\EmployeeAbsenceRequest;
use App\Models\EmployeeContract;
use App\Models\EmployeeResignation;
use App\Models\EmployeeTermination;
use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetHRMetrics extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Get aggregated HRWork metrics for a date range: workforce status, attendance, leave/absence, payroll, contracts, warning letters, resignations, and terminations. Use this for HR summaries and dashboards rather than individual record lookup.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-employee')) {
            return $denied;
        }

        $from = $this->optionalDate($request['from_date'] ?? null) ?? now()->startOfMonth()->toDateString();
        $to = $this->optionalDate($request['to_date'] ?? null) ?? now()->endOfMonth()->toDateString();

        $metrics = [
            'period' => [
                'from' => $from,
                'to' => $to,
            ],
            'workforce' => [
                'total_employees' => Employees::query()->count(),
                'active' => Employees::query()->where('status_employee', 'active')->count(),
                'onboarding' => Employees::query()->where('status_employee', 'onboarding')->count(),
                'inactive' => Employees::query()->where('status_employee', 'inactive')->count(),
                'resign' => Employees::query()->where('status_employee', 'resign')->count(),
                'terminated' => Employees::query()->where('status_employee', 'terminated')->count(),
            ],
            'attendance' => [
                'records' => Attendances::query()->whereBetween('date', [$from, $to])->count(),
                'days_with_check_in' => Attendances::query()->whereBetween('date', [$from, $to])->whereNotNull('check_in_at')->count(),
                'late_days' => Attendances::query()->whereBetween('date', [$from, $to])->where('late_minutes', '>', 0)->count(),
                'total_late_minutes' => (int) Attendances::query()->whereBetween('date', [$from, $to])->sum('late_minutes'),
                'total_work_duration_minutes' => (int) Attendances::query()->whereBetween('date', [$from, $to])->sum('work_duration'),
            ],
            'leave' => [
                'leave_requests' => LeaveRequest::query()->where(function ($q) use ($from, $to) {
                    $q->whereDate('end_date', '>=', $from)->whereDate('start_date', '<=', $to);
                })->count(),
                'pending_leave_requests' => LeaveRequest::query()->where('status', 'pending')->whereDate('end_date', '>=', $from)->whereDate('start_date', '<=', $to)->count(),
                'approved_leave_days' => (int) LeaveRequest::query()->where('status', 'approved')->whereDate('end_date', '>=', $from)->whereDate('start_date', '<=', $to)->sum('total_days'),
                'absence_requests' => EmployeeAbsenceRequest::query()->whereBetween('date', [$from, $to])->count(),
                'pending_absence_requests' => EmployeeAbsenceRequest::query()->where('status', 'pending')->whereBetween('date', [$from, $to])->count(),
            ],
            'contracts' => [
                'active_contracts' => EmployeeContract::query()->where('status', 'active')->count(),
                'started_in_period' => EmployeeContract::query()->whereBetween('start_date', [$from, $to])->count(),
                'ending_in_period' => EmployeeContract::query()->whereNotNull('end_date')->whereBetween('end_date', [$from, $to])->count(),
            ],
            'warning_letters' => [
                'issued' => EmployeeWarningLetter::query()->where('status', 'issued')->whereBetween('issued_date', [$from, $to])->count(),
                'draft' => EmployeeWarningLetter::query()->where('status', 'draft')->whereBetween('issued_date', [$from, $to])->count(),
                'cancelled' => EmployeeWarningLetter::query()->where('status', 'cancelled')->whereBetween('issued_date', [$from, $to])->count(),
            ],
            'resignations' => [
                'submitted' => EmployeeResignation::query()->whereBetween('proposed_last_working_date', [$from, $to])->where('status', 'submitted')->count(),
                'approved' => EmployeeResignation::query()->whereBetween('proposed_last_working_date', [$from, $to])->where('status', 'approved')->count(),
                'completed' => EmployeeResignation::query()->whereBetween('proposed_last_working_date', [$from, $to])->where('status', 'completed')->count(),
            ],
            'terminations' => [
                'in_progress' => EmployeeTermination::query()->whereBetween('effective_date', [$from, $to])->where('status', 'in_progress')->count(),
                'completed' => EmployeeTermination::query()->whereBetween('effective_date', [$from, $to])->where('status', 'completed')->count(),
                'cancelled' => EmployeeTermination::query()->whereBetween('effective_date', [$from, $to])->where('status', 'cancelled')->count(),
            ],
        ];

        if (auth()->user()?->can('view-payroll')) {
            $periods = PayrollPeriod::query()
                ->whereDate('end_date', '>=', $from)
                ->whereDate('start_date', '<=', $to)
                ->pluck('id');

            $metrics['payroll'] = [
                'periods' => $periods->count(),
                'payroll_records' => Payroll::query()->whereIn('payroll_period_id', $periods)->count(),
                'processed' => Payroll::query()->whereIn('payroll_period_id', $periods)->where('status', 'processed')->count(),
                'paid' => Payroll::query()->whereIn('payroll_period_id', $periods)->where('status', 'paid')->count(),
                'net_amount_total' => (string) Payroll::query()->whereIn('payroll_period_id', $periods)->sum('net_amount'),
            ];
        }

        return $this->json([
            'success' => true,
            'metrics' => $metrics,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
        ];
    }
}
