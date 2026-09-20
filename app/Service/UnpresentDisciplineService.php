<?php

namespace App\Service;

use App\Models\Employees;
use App\Models\UnpresentDisciplineRule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UnpresentDisciplineService
{
    public function getCandidates(?Carbon $date = null): Collection
    {
        $date ??= now();
        $rule = UnpresentDisciplineRule::query()->first();

        if (!$rule) {
            return collect();
        }

        $monthStart = $date->copy()->startOfMonth()->startOfDay();
        $today = $date->copy()->startOfDay();

        $employees = Employees::query()
            ->with(['user', 'employeeContract'])
            ->whereHas('user.roles', fn ($query) => $query->where('name', 'employee'))
            ->whereHas('employeeContract', function ($query) use ($monthStart, $today) {
                $query->where('status', 'active')
                    ->whereDate('start_date', '<=', $today)
                    ->where(function ($query) use ($monthStart) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $monthStart);
                    });
            })
            ->get();

        $dailyStatusService = app(EmployeeDailyStatusService::class);

        return $employees
            ->map(function (Employees $employee) use ($dailyStatusService, $monthStart, $today, $rule) {
                $statuses = $dailyStatusService->getStatuses($employee, $monthStart, $today);

                $unpresentStatuses = $statuses
                    ->filter(fn (array $state) => $state['is_unpresent'] === true)
                    ->values();

                $count = $unpresentStatuses->count();

                if ($count < (int) $rule->threshold) {
                    return null;
                }

                return [
                    'employee' => $employee,
                    'contract' => $employee->employeeContract
                        ->filter(fn ($contract) => $contract->status === 'active')
                        ->sortByDesc('start_date')
                        ->first(),
                    'period_start' => $monthStart->toDateString(),
                    'period_end' => $today->toDateString(),
                    'threshold' => (int) $rule->threshold,
                    'unpresent_count' => $count,
                    'dates' => $unpresentStatuses->pluck('date')->values()->all(),
                ];
            })
            ->filter()
            ->values();
    }
}
