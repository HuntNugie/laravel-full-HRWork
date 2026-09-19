<?php

namespace App\Service;

use App\Models\Attendances;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\UnpresentDisciplineRule;
use App\Models\WorkTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

        $monthStart = $date->copy()->startOfMonth();
        $today = $date->copy()->startOfDay();

        /*
        |--------------------------------------------------------------------------
        | WORK TIME
        |--------------------------------------------------------------------------
        */

        $workingDays = WorkTime::query()
            ->where('is_working_day', true)
            ->pluck('day_of_week')
            ->map(fn($day) => strtolower(trim($day)))
            ->flip();

        /*
        |--------------------------------------------------------------------------
        | HOLIDAYS
        |--------------------------------------------------------------------------
        */

        $holidays = Holidays::query()
            ->whereBetween('date', [
                $monthStart->toDateString(),
                $today->toDateString(),
            ])
            ->pluck('date')
            ->mapWithKeys(function ($date) {
                return [
                    Carbon::parse($date)->toDateString() => true,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEES
        |--------------------------------------------------------------------------
        */

        $employees = Employees::query()
            ->with([
                'user',
                'employeeContract',
            ])
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'employee');
            })
            ->whereHas('employeeContract', function ($query) use ($monthStart, $today) {
                $query
                    ->where('status', 'active')
                    ->whereDate('start_date', '<=', $today)
                    ->where(function ($query) use ($monthStart) {
                        $query
                            ->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $monthStart);
                    });
            })
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CALCULATE
        |--------------------------------------------------------------------------
        */

        return $employees
            ->map(function (Employees $employee) use (
                $monthStart,
                $today,
                $rule,
                $workingDays,
                $holidays
            ) {
                /*
                |--------------------------------------------------------------------------
                | ACTIVE CONTRACT YANG BERLAKU
                |--------------------------------------------------------------------------
                */

                $contract = $employee->employeeContract
                    ->filter(function ($contract) use ($monthStart, $today) {
                        if ($contract->status !== 'active') {
                            return false;
                        }

                        if ($contract->start_date->gt($today)) {
                            return false;
                        }

                        if (
                            $contract->end_date &&
                            $contract->end_date->lt($monthStart)
                        ) {
                            return false;
                        }

                        return true;
                    })
                    ->sortByDesc('start_date')
                    ->first();

                if (!$contract) {
                    return null;
                }

                /*
                |--------------------------------------------------------------------------
                | EFFECTIVE PERIOD
                |--------------------------------------------------------------------------
                */

                $effectiveStart = $contract->start_date->greaterThan($monthStart)
                    ? $contract->start_date->copy()
                    : $monthStart->copy();

                $effectiveEnd = $today->copy();

                if (
                    $contract->end_date &&
                    $contract->end_date->lessThan($effectiveEnd)
                ) {
                    $effectiveEnd = $contract->end_date->copy();
                }

                if ($effectiveStart->gt($effectiveEnd)) {
                    return null;
                }

                /*
                |--------------------------------------------------------------------------
                | ATTENDANCE
                |--------------------------------------------------------------------------
                */

                $attendances = Attendances::query()
                    ->where('employee_id', $employee->id)
                    ->whereBetween('date', [
                        $effectiveStart->toDateString(),
                        $effectiveEnd->toDateString(),
                    ])
                    ->whereNotNull('check_in_at')
                    ->pluck('date')
                    ->mapWithKeys(function ($date) {
                        return [
                            Carbon::parse($date)->toDateString() => true,
                        ];
                    });

                /*
                |--------------------------------------------------------------------------
                | UNPRESENT DATES
                |--------------------------------------------------------------------------
                */

                $unpresentDates = collect(
                    CarbonPeriod::create(
                        $effectiveStart,
                        $effectiveEnd
                    )
                )->filter(function (Carbon $date) use (
                    $workingDays,
                    $holidays,
                    $attendances
                ) {
                    /*
                    | Map Carbon ISO day:
                    | 1 = senin
                    | 2 = selasa
                    | ...
                    | 7 = minggu
                    */
                    $dayNames = [
                        1 => 'senin',
                        2 => 'selasa',
                        3 => 'rabu',
                        4 => 'kamis',
                        5 => 'jumat',
                        6 => 'sabtu',
                        7 => 'minggu',
                    ];

                    $dayName = $dayNames[$date->dayOfWeekIso];

                    /*
                    |--------------------------------------------------------------------------
                    | BUKAN HARI KERJA
                    |--------------------------------------------------------------------------
                    */

                    if (!$workingDays->has($dayName)) {
                        return false;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | HOLIDAY
                    |--------------------------------------------------------------------------
                    */

                    if ($holidays->has($date->toDateString())) {
                        return false;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | SUDAH CHECK IN
                    |--------------------------------------------------------------------------
                    */

                    if ($attendances->has($date->toDateString())) {
                        return false;
                    }

                    return true;
                })->values();

                $count = $unpresentDates->count();

                /*
                |--------------------------------------------------------------------------
                | CHECK THRESHOLD
                |--------------------------------------------------------------------------
                */

                if ($count < $rule->threshold) {
                    return null;
                }

                return [
                    'employee' => $employee,
                    'contract' => $contract,

                    'period_start' => $effectiveStart->toDateString(),
                    'period_end' => $effectiveEnd->toDateString(),

                    'threshold' => (int) $rule->threshold,
                    'unpresent_count' => $count,

                    'dates' => $unpresentDates
                        ->map(fn(Carbon $date) => $date->toDateString())
                        ->values()
                        ->all(),
                ];
            })
            ->filter()
            ->values();
    }
}
