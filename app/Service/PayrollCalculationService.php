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
     * Calculate payroll totals for one employee within a payroll period.
     *
     * Salary and benefit amounts are segmented by the contract that is
     * effective on each payroll date. Late discipline is calculated once
     * across the entire resolved period so a contract transition cannot reset
     * the monthly late threshold.
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
     *   salary_items:Collection,
     *   benefit_items:Collection,
     *   next_sort_order:int
     * }
     */
    public function calculate(
        Employees $employee,
        EmployeeContract $contract,
        PayrollPeriod $period,
    ): array {
        $contracts = $this->effectiveContracts(
            employee: $employee,
            period: $period,
        );

        $statuses = $this->dailyStatusService->getStatuses(
            employee: $employee,
            startDate: $period->start_date,
            endDate: $period->end_date,
        );

        $resolvedStatuses = $statuses
            ->reject(
                fn(array $state) =>
                    $state['status'] === EmployeeDailyStatusService::STATUS_PENDING
            )
            ->values();

        $workingStatuses = $resolvedStatuses
            ->filter(
                fn(array $state) =>
                    $state['is_working_day'] === true
                    && $state['contract_id'] !== null
            )
            ->values();

        $workingDays = $workingStatuses->count();

        $presentDays = $workingStatuses
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

        $paidLeaveDays = $workingStatuses
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

        /*
        |--------------------------------------------------------------------------
        | LATE DISCIPLINE
        |--------------------------------------------------------------------------
        |
        | Calculated against the full resolved period, not per contract.
        | This keeps monthly thresholds continuous across contract changes.
        |
        */
        $lateDiscipline = $this->lateDisciplineService->calculateFromStatuses(
            statuses: $resolvedStatuses,
        );

        $lateDays = $lateDiscipline['late_count'];
        $lateDeductionTotal = $lateDiscipline['deduction_amount'];
        $lateDeductionItems = $lateDiscipline['items'];

        /*
        |--------------------------------------------------------------------------
        | CONTRACT-SEGMENTED SALARY & BENEFITS
        |--------------------------------------------------------------------------
        */

        $contractsById = $contracts->keyBy('id');

        $salaryAmount = 0.0;
        $benefitTotal = 0.0;
        $salaryItems = collect();
        $benefitItems = collect();

        $workingStatuses
            ->groupBy('contract_id')
            ->each(function (Collection $contractStatuses, $contractId) use (
                $contractsById,
                &$salaryAmount,
                &$benefitTotal,
                $employee,
                &$salaryItems,
                &$benefitItems
            ): void {
                /** @var EmployeeContract|null $effectiveContract */
                $effectiveContract = $contractsById->get((int) $contractId);

                if (!$effectiveContract) {
                    return;
                }

                $segmentPaidDays = $contractStatuses
                    ->filter(
                        fn(array $state) =>
                            in_array(
                                $state['status'],
                                [
                                    EmployeeDailyStatusService::STATUS_PRESENT,
                                    EmployeeDailyStatusService::STATUS_LATE,
                                    EmployeeDailyStatusService::STATUS_PAID_LEAVE,
                                ],
                                true
                            )
                    )
                    ->count();

                if ($segmentPaidDays <= 0) {
                    return;
                }

                $salaryDaily = (float) $effectiveContract->salary_daily;
                $segmentSalary = $segmentPaidDays * $salaryDaily;

                $salaryAmount += $segmentSalary;

                $salaryItems->push([
                    'name' => 'Gaji Harian',
                    'amount' => $segmentSalary,
                    'quantity' => $segmentPaidDays,
                    'rate' => $salaryDaily,
                    'contract_id' => $effectiveContract->id,
                    'contract_start_date' => $effectiveContract->start_date,
                    'contract_end_date' => $effectiveContract->end_date,
                    'position_name' => $effectiveContract->position_name,
                ]);

                foreach ($effectiveContract->benefits as $benefit) {
                    $benefitDaily = (float) ($benefit->pivot->amount ?? 0);
                    $benefitAmount = $segmentPaidDays * $benefitDaily;

                    if ($benefitAmount <= 0) {
                        continue;
                    }

                    $benefitTotal += $benefitAmount;

                    $benefitItems->push([
                        'benefit' => $benefit,
                        'amount' => $benefitAmount,
                        'quantity' => $segmentPaidDays,
                        'rate' => $benefitDaily,
                        'contract_id' => $effectiveContract->id,
                        'contract_start_date' => $effectiveContract->start_date,
                        'contract_end_date' => $effectiveContract->end_date,
                        'position_name' => $effectiveContract->position_name,
                    ]);
                }
            });

        return [
            'salary_daily' => (float) $contract->salary_daily,
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
            'salary_items' => $salaryItems,
            'benefit_items' => $benefitItems,
            'next_sort_order' => 1
                + $salaryItems->count()
                + $benefitItems->count()
                + $lateDeductionItems->count(),
        ];
    }

    /**
     * Load every non-draft contract that overlaps the payroll period.
     *
     * Historical expired/terminated contracts are included because the
     * employee's Daily Status resolves the contract by effective date.
     */
    private function effectiveContracts(
        Employees $employee,
        PayrollPeriod $period,
    ): Collection {
        return $employee->employeeContract()
            ->whereIn('status', ['active', 'expired', 'terminated'])
            ->whereDate('start_date', '<=', $period->end_date->toDateString())
            ->where(function ($query) use ($period) {
                $query
                    ->whereNull('end_date')
                    ->orWhereDate(
                        'end_date',
                        '>=',
                        $period->start_date->toDateString()
                    );
            })
            ->with('benefits')
            ->orderByDesc('start_date')
            ->get();
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
            'salary_items' => collect(),
            'benefit_items' => collect(),
            'next_sort_order' => 1,
        ];
    }
}
