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
        return $this->calculateFromStatuses(
            statuses: app(EmployeeDailyStatusService::class)->getStatuses(
                employee: $employee,
                startDate: $startDate,
                endDate: $endDate,
            ),
        );
    }

    /**
     * Calculate the payroll deduction amount for a known late count.
     */
    public function calculateAmountFromCount(
        int $lateCount,
        LateDisciplineRule $rule,
    ): float {
        $this->validateRule($rule);

        if ($lateCount < 1) {
            return 0.0;
        }

        return intdiv($lateCount, (int) $rule->threshold)
            * (float) $rule->action_amount;
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
        $rule = $rule
            ? $this->validateRule($rule)
            : $this->resolveActiveRule();

        $threshold = (int) $rule->threshold;
        $actionAmount = (float) $rule->action_amount;

        $lateStatuses = $statuses
            ->filter(fn(array $state) => ($state['is_late'] ?? false) === true)
            ->sortBy('date')
            ->values();

        $lateDates = $lateStatuses
            ->pluck('date')
            ->values();

        $items = $lateStatuses
            ->groupBy(
                fn(array $state) =>
                    substr((string) $state['date'], 0, 7)
            )
            ->map(
                function (Collection $states, string $monthKey) use (
                    $threshold,
                    $actionAmount
                ) {
                    $lateCount = $states->count();
                    $deductionUnits = intdiv(
                        $lateCount,
                        $threshold
                    );

                    if ($deductionUnits <= 0) {
                        return null;
                    }

                    return [
                        'month' => $monthKey,
                        'late_count' => $lateCount,
                        'deduction_units' => $deductionUnits,
                        'amount' => $deductionUnits * $actionAmount,
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
            'deduction_amount' => $items->sum(
                fn(array $item) => $item['amount']
            ),
            'late_dates' => $lateDates,
            'items' => $items,
        ];
    }

    /**
     * Resolve the single active late-discipline rule.
     *
     * The current HRWork business design has one effective late-discipline
     * rule. Multiple active rules would make the calculation ambiguous.
     */
    private function resolveActiveRule(): LateDisciplineRule
    {
        $activeRules = LateDisciplineRule::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        if ($activeRules->isEmpty()) {
            throw new \RuntimeException(
                'Aturan keterlambatan aktif belum dikonfigurasi.'
            );
        }

        if ($activeRules->count() > 1) {
            throw new \RuntimeException(
                'Terdapat lebih dari satu aturan keterlambatan aktif. Aktifkan hanya satu aturan.'
            );
        }

        return $this->validateRule($activeRules->first());
    }

    /**
     * Validate configuration before it participates in payroll/discpline math.
     */
    private function validateRule(
        LateDisciplineRule $rule
    ): LateDisciplineRule {
        if ($rule->status !== 'active') {
            throw new \RuntimeException(
                'Aturan keterlambatan yang digunakan harus berstatus aktif.'
            );
        }

        if ((int) $rule->threshold < 1) {
            throw new \RuntimeException(
                'Threshold keterlambatan harus lebih besar dari 0.'
            );
        }

        if ($rule->period_type !== 'monthly') {
            throw new \RuntimeException(
                'Periode aturan keterlambatan saat ini harus bulanan.'
            );
        }

        if ($rule->action_type !== 'payroll_deduction') {
            throw new \RuntimeException(
                'Konsekuensi aturan keterlambatan saat ini harus berupa potongan gaji.'
            );
        }

        if ((float) $rule->action_amount < 0) {
            throw new \RuntimeException(
                'Nominal potongan keterlambatan tidak boleh negatif.'
            );
        }

        return $rule;
    }
}
