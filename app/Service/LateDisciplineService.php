<?php

namespace App\Service;

use App\Models\Employees;
use App\Models\LateDisciplineRule;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class LateDisciplineService
{
    /**
     * Calculate late discipline for an employee across a date range.
     *
     * The daily attendance state is always sourced from
     * EmployeeDailyStatusService. Only states marked as late participate.
     *
     * @return array{
     *   threshold:int,
     *   action_amount:float,
     *   late_count:int,
     *   deduction_units:int,
     *   deduction_amount:float,
     *   late_dates:Collection<int,string>,
     *   items:Collection<int,array{month:string,late_count:int,deduction_units:int,amount:float}>
     * }
     */
    public function calculate(
        Employees $employee,
        CarbonInterface|string $startDate,
        CarbonInterface|string $endDate,
    ): array {
        $rule = LateDisciplineRule::query()->first();

        if (!$rule) {
            throw new \RuntimeException(
                'Aturan keterlambatan belum dikonfigurasi.'
            );
        }

        return $this->calculateFromStatuses(
            statuses: app(EmployeeDailyStatusService::class)->getStatuses(
                employee: $employee,
                startDate: $startDate,
                endDate: $endDate,
            ),
            rule: $rule,
        );
    }

    /**
     * Calculate late discipline from already resolved daily states.
     *
     * This method lets Payroll reuse the same Daily Status collection without
     * querying the source tables a second time.
     */
    public function calculateFromStatuses(
        Collection $statuses,
        ?LateDisciplineRule $rule = null,
    ): array {
        $rule ??= LateDisciplineRule::query()->first();

        if (!$rule) {
            throw new \RuntimeException(
                'Aturan keterlambatan belum dikonfigurasi.'
            );
        }

        $threshold = (int) $rule->threshold;
        $actionAmount = (float) $rule->action_amount;

        if ($threshold < 1) {
            throw new \RuntimeException(
                'Threshold keterlambatan harus lebih besar dari 0.'
            );
        }

        $lateStatuses = $statuses
            ->filter(fn(array $state) => $state['is_late'] === true)
            ->values();

        $lateDates = $lateStatuses
            ->pluck('date')
            ->values();

        $deductionAmount = 0.0;

        $items = $lateStatuses
            ->groupBy(
                fn(array $state) =>
                    substr((string) $state['date'], 0, 7)
            )
            ->map(
                function (Collection $states, string $monthKey) use (
                    $threshold,
                    $actionAmount,
                    &$deductionAmount
                ) {
                    $lateCount = $states->count();
                    $deductionUnits = intdiv(
                        $lateCount,
                        $threshold
                    );

                    if ($deductionUnits <= 0) {
                        return null;
                    }

                    $amount = $deductionUnits * $actionAmount;
                    $deductionAmount += $amount;

                    return [
                        'month' => $monthKey,
                        'late_count' => $lateCount,
                        'deduction_units' => $deductionUnits,
                        'amount' => $amount,
                    ];
                }
            )
            ->filter()
            ->values();

        return [
            'threshold' => $threshold,
            'action_amount' => $actionAmount,
            'late_count' => $lateStatuses->count(),
            'deduction_units' => $items->sum(
                fn(array $item) => $item['deduction_units']
            ),
            'deduction_amount' => $deductionAmount,
            'late_dates' => $lateDates,
            'items' => $items,
        ];
    }
}
