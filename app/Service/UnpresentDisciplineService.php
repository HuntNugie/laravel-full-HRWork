<?php

namespace App\Service;

use App\Models\Attendances;
use App\Models\EmployeeAbsenceRequest;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\LeaveRequest;
use App\Models\UnpresentDisciplineRule;
use App\Models\WorkTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class UnpresentDisciplineService
{
    /**
     * Return employees whose past working-day Daily Status reaches the
     * configured unpresent threshold in the current rule period.
     *
     * All source data is loaded in bulk for the eligible employees. The
     * previous implementation called EmployeeDailyStatusService once per
     * employee, which caused several queries to run for every employee.
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
        $startDate = $monthStart->toDateString();
        $endDate = $today->toDateString();

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE ELIGIBILITY
        |--------------------------------------------------------------------------
        |
        | Contract dipilih berdasarkan tanggal efektif, termasuk expired dan
        | terminated untuk kebutuhan histori Daily Status.
        |
        */
        $employees = Employees::query()
            ->with([
                'user',
                'employeeContract' => function ($query) use ($monthStart, $today) {
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
                        })
                        ->orderByDesc('start_date');
                },
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

        if ($employees->isEmpty()) {
            return collect();
        }

        $employeeIds = $employees->pluck('id')->all();

        /*
        |--------------------------------------------------------------------------
        | SHARED SOURCE DATA
        |--------------------------------------------------------------------------
        |
        | Semua data yang sebelumnya diambil satu employee satu kali sekarang
        | diambil sekali untuk seluruh kandidat.
        |
        */
        $workTimes = WorkTime::query()
            ->get()
            ->keyBy(
                fn($workTime) => strtolower(trim($workTime->day_of_week))
            );

        $holidays = Holidays::query()
            ->whereBetween('date', [
                $startDate,
                $endDate,
            ])
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $attendanceByEmployee = Attendances::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('date', [
                $startDate,
                $endDate,
            ])
            ->whereNotNull('check_in_at')
            ->get([
                'id',
                'employee_id',
                'date',
            ])
            ->groupBy('employee_id')
            ->map(
                fn(Collection $attendances) => $attendances->keyBy(
                    fn($attendance) => Carbon::parse($attendance->date)->toDateString()
                )
            );

        $leaveRequestsByEmployee = LeaveRequest::query()
            ->whereIn('employee_id', $employeeIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->get([
                'id',
                'employee_id',
                'start_date',
                'end_date',
            ])
            ->groupBy('employee_id');

        $absenceByEmployee = EmployeeAbsenceRequest::query()
            ->whereIn('employee_id', $employeeIds)
            ->where('status', 'approved')
            ->whereBetween('date', [
                $startDate,
                $endDate,
            ])
            ->get([
                'id',
                'employee_id',
                'date',
            ])
            ->groupBy('employee_id')
            ->map(
                fn(Collection $absences) => $absences->keyBy(
                    fn($absence) => Carbon::parse($absence->date)->toDateString()
                )
            );

        /*
        |--------------------------------------------------------------------------
        | SHARED CALENDAR DATA
        |--------------------------------------------------------------------------
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

        $days = collect(CarbonPeriod::create($monthStart, $today))
            ->map(function (Carbon $date) use (
                $dayNames,
                $workTimes,
                $holidays
            ) {
                $date = Carbon::instance($date)->startOfDay();
                $dateKey = $date->toDateString();
                $workTime = $workTimes->get(
                    $dayNames[$date->dayOfWeekIso]
                );

                return [
                    'date' => $date,
                    'date_key' => $dateKey,
                    'is_working_day' => $workTime?->is_working_day === true,
                    'is_holiday' => $holidays->has($dateKey),
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | CALCULATE CANDIDATES IN MEMORY
        |--------------------------------------------------------------------------
        */
        return $employees
            ->map(function (Employees $employee) use (
                $days,
                $monthStart,
                $today,
                $threshold,
                $attendanceByEmployee,
                $leaveRequestsByEmployee,
                $absenceByEmployee
            ) {
                $contracts = $employee->employeeContract;

                $attendances = $attendanceByEmployee->get(
                    $employee->id,
                    collect()
                );

                $leaveDates = collect();

                foreach (
                    $leaveRequestsByEmployee->get(
                        $employee->id,
                        collect()
                    ) as $leaveRequest
                ) {
                    $leaveStart = max(
                        Carbon::parse($leaveRequest->start_date)->startOfDay(),
                        $monthStart
                    );

                    $leaveEnd = min(
                        Carbon::parse($leaveRequest->end_date)->startOfDay(),
                        $today
                    );

                    if ($leaveStart->gt($leaveEnd)) {
                        continue;
                    }

                    foreach (
                        CarbonPeriod::create($leaveStart, $leaveEnd) as $date
                    ) {
                        $leaveDates->put(
                            Carbon::instance($date)->toDateString(),
                            true
                        );
                    }
                }

                $absences = $absenceByEmployee->get(
                    $employee->id,
                    collect()
                );

                $unpresentDates = [];

                foreach ($days as $day) {
                    $date = $day['date'];

                    // Daily Status menganggap hari ini sebagai pending, bukan unpresent.
                    if ($date->gte($today)) {
                        continue;
                    }

                    if (
                        !$day['is_working_day']
                        || $day['is_holiday']
                    ) {
                        continue;
                    }

                    // Gunakan contract yang efektif pada tanggal tersebut.
                    $contract = $contracts->first(
                        function ($contract) use ($date) {
                            return $contract->start_date->lte($date)
                                && (
                                    !$contract->end_date
                                    || $contract->end_date->gte($date)
                                );
                        }
                    );

                    if (!$contract) {
                        continue;
                    }

                    // Attendance dengan check-in mengambil precedence.
                    if ($attendances->has($day['date_key'])) {
                        continue;
                    }

                    if ($leaveDates->has($day['date_key'])) {
                        continue;
                    }

                    if ($absences->has($day['date_key'])) {
                        continue;
                    }

                    $unpresentDates[] = $day['date_key'];
                }

                $count = count($unpresentDates);

                if ($count < $threshold) {
                    return null;
                }

                $contract = $contracts->first(
                    function ($contract) use ($today) {
                        return $contract->start_date->lte($today)
                            && (
                                !$contract->end_date
                                || $contract->end_date->gte($today)
                            );
                    }
                );

                return [
                    'employee' => $employee,
                    'contract' => $contract,
                    'period_start' => $monthStart->toDateString(),
                    'period_end' => $today->toDateString(),
                    'threshold' => $threshold,
                    'unpresent_count' => $count,
                    'dates' => $unpresentDates,
                ];
            })
            ->filter()
            ->values();
    }
}
