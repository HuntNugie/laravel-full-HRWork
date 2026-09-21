<?php

namespace App\Service;

use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;

class PayrollCalculationService
{
    public function __construct(
        private readonly EmployeeDailyStatusService $dailyStatusService,
        private readonly LateDisciplineService $lateDisciplineService,
    ) {
    }

    /**
     * Calculate payroll totals for one employee/contract within a payroll period.
     *
     * Pending dates are intentionally excluded from working/absent-day totals.
     * They represent the current/future work schedule that has not been resolved
     * yet and therefore must not reduce payroll as an absence.
     *
     * @return array{
     *   salary_daily:float,
     *   salary_amount:float,
     *   working_days:int,
     *   present_days:int,
     *   late_days:int,
     *   absent_days:int,
     *   paid_leave_days:int,
     *   paid_days:int,
     *   benefit_total:float,
     *   late_deduction_total:float,
     *   late_deduction_items:Collection,
     *   benefit_items:Collection,
     *   next_sort_order:int
     * }
     */
    public function calculate(
        Employees $employee,
        EmployeeContract $contract,
        PayrollPeriod $period,
    ): array {
        $eligibleStart = $contract->start_date->greaterThan($period->start_date)
            ? $contract->start_date->copy()
            : $period->start_date->copy();

        $eligibleEnd = $contract->end_date && $contract->end_date->lessThan($period->end_date)
            ? $contract->end_date->copy()
            : $period->end_date->copy();

        if ($eligibleStart->gt($eligibleEnd)) {
            return $this->emptyResult($contract);
        }

        $statuses = $this->dailyStatusService->getStatuses(
            employee: $employee,
            startDate: $eligibleStart,
            endDate: $eligibleEnd,
        );

        $resolvedStatuses = $statuses
            ->reject(
                fn(array $state) =>
                    $state['status'] === EmployeeDailyStatusService::STATUS_PENDING
            )
            ->values();

        $workingDays = $resolvedStatuses
            ->filter(fn(array $state) => $state['is_working_day'] === true)
            ->count();

        $presentDays = $resolvedStatuses
            ->filter(
                fn(array $state) =>
                    in_array(
                        $state['status'],
                        [
                            EmployeeDailyStatusService::STATUS_PRESENT,
                            EmployeeDailyStatusService::STATUS_LATE,
                        ],
                        true
                    )
            )
            ->count();

        $lateDiscipline = $this->lateDisciplineService->calculateFromStatuses(
            statuses: $resolvedStatuses,
        );

        $lateDays = $lateDiscipline['late_count'];
        $lateDeductionTotal = $lateDiscipline['deduction_amount'];
        $lateDeductionItems = $lateDiscipline['items'];

        $paidLeaveDays = $resolvedStatuses
            ->filter(
                fn(array $state) =>
                    $state['status'] === EmployeeDailyStatusService::STATUS_PAID_LEAVE
            )
            ->count();

        $paidDays = $presentDays + $paidLeaveDays;

        $absentDays = max(
            0,
            $workingDays - $paidDays
        );

        $salaryDaily = (float) $contract->salary_daily;
        $salaryAmount = $paidDays * $salaryDaily;

        $benefitTotal = 0.0;
        $benefitItems = collect();

        foreach ($contract->benefits as $benefit) {
            $benefitDaily = (float) ($benefit->pivot->amount ?? 0);
            $benefitAmount = $paidDays * $benefitDaily;

            if ($benefitAmount <= 0) {
                continue;
            }

            $benefitTotal += $benefitAmount;

            $benefitItems->push([
                'benefit' => $benefit,
                'amount' => $benefitAmount,
                'quantity' => $paidDays,
                'rate' => $benefitDaily,
            ]);
        }

        return [
            'salary_daily' => $salaryDaily,
            'salary_amount' => $salaryAmount,
            'working_days' => $workingDays,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'absent_days' => $absentDays,
            'paid_leave_days' => $paidLeaveDays,
            'paid_days' => $paidDays,
            'benefit_total' => $benefitTotal,
            'late_deduction_total' => $lateDeductionTotal,
            'late_deduction_items' => $lateDeductionItems,
            'benefit_items' => $benefitItems,
            'next_sort_order' => 2
                + $benefitItems->count()
                + $lateDeductionItems->count(),
        ];
    }

    private function emptyResult(EmployeeContract $contract): array
    {
        return [
            'salary_daily' => (float) $contract->salary_daily,
            'salary_amount' => 0.0,
            'working_days' => 0,
            'present_days' => 0,
            'late_days' => 0,
            'absent_days' => 0,
            'paid_leave_days' => 0,
            'paid_days' => 0,
            'benefit_total' => 0.0,
            'late_deduction_total' => 0.0,
            'late_deduction_items' => collect(),
            'benefit_items' => collect(),
            'next_sort_order' => 2,
        ];
    }
}
