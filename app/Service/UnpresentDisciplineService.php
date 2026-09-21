<?php

namespace App\Service;

use App\Models\Employees;
use App\Models\UnpresentDisciplineRule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UnpresentDisciplineService
{
    /**
     * Return employees whose past working-day Daily Status reaches the
     * configured unpresent threshold in the current rule period.
     *
     * The rule is intentionally a single configuration row, just like the
     * current Late Discipline design.
     */
    public function getCandidates(?Carbon $date = null): Collection
    {
        $today = ($date ?? now())->copy()->startOfDay();

        $rule = UnpresentDisciplineRule::query()
            ->firstOrFail();

        $threshold = (int) $rule->threshold;

        if ($threshold < 1) {
            throw new \RuntimeException(
                'Threshold ketidakhadiran harus lebih besar dari 0.'
            );
        }

        if ($rule->period_type !== 'monthly') {
            throw new \RuntimeException(
                'Periode aturan ketidakhadiran saat ini harus bulanan.'
            );
        }

        $monthStart = $today->copy()->startOfMonth();

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE ELIGIBILITY
        |--------------------------------------------------------------------------
        |
        | Jangan hanya mencari contract active. Daily Status menentukan
        | contract berdasarkan tanggal efektif, termasuk expired/terminated.
        |
        */
        $employees = Employees::query()
            ->with([
                'user',
                'employeeContract',
            ])
            ->whereHas(
                'user.roles',
                fn($query) => $query->where('name', 'employee')
            )
            ->whereHas('employeeContract', function ($query) use (
                $monthStart,
                $today
            ) {
                $query
                    ->whereIn('status', [
                        'active',
                        'expired',
                        'terminated',
                    ])
                    ->whereDate(
                        'start_date',
                        '<=',
                        $today->toDateString()
                    )
                    ->where(function ($query) use ($monthStart) {
                        $query
                            ->whereNull('end_date')
                            ->orWhereDate(
                                'end_date',
                                '>=',
                                $monthStart->toDateString()
                            );
                    });
            })
            ->get();

        $dailyStatusService = app(EmployeeDailyStatusService::class);

        return $employees
            ->map(function (Employees $employee) use (
                $dailyStatusService,
                $monthStart,
                $today,
                $threshold
            ) {
                $statuses = $dailyStatusService->getStatuses(
                    employee: $employee,
                    startDate: $monthStart,
                    endDate: $today,
                );

                $unpresentStatuses = $statuses
                    ->filter(
                        fn(array $state) =>
                            ($state['is_unpresent'] ?? false) === true
                    )
                    ->sortBy('date')
                    ->values();

                $count = $unpresentStatuses->count();

                if ($count < $threshold) {
                    return null;
                }

                $contract = $employee->employeeContract
                    ->filter(function ($contract) use ($today) {
                        $startDate = $contract->start_date;
                        $endDate = $contract->end_date;

                        return $contract->status !== 'draft'
                            && $startDate->lte($today)
                            && (!$endDate || $endDate->gte($today));
                    })
                    ->sortByDesc('start_date')
                    ->first();

                return [
                    'employee' => $employee,
                    'contract' => $contract,
                    'period_start' => $monthStart->toDateString(),
                    'period_end' => $today->toDateString(),
                    'threshold' => $threshold,
                    'unpresent_count' => $count,
                    'dates' => $unpresentStatuses
                        ->pluck('date')
                        ->values()
                        ->all(),
                ];
            })
            ->filter()
            ->values();
    }
}
