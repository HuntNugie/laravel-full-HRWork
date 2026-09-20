<?php

namespace App\Service;

use App\Models\EmployeeAbsenceRequest;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\LeaveRequest;
use App\Models\WorkTime;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class EmployeeDailyStatusService
{
    public const STATUS_OUTSIDE_CONTRACT = 'outside_contract';
    public const STATUS_NON_WORKING = 'non_working';
    public const STATUS_HOLIDAY = 'holiday';
    public const STATUS_PRESENT = 'present';
    public const STATUS_LATE = 'late';
    public const STATUS_PAID_LEAVE = 'paid_leave';
    public const STATUS_ABSENCE_SICK = 'absence_sick';
    public const STATUS_ABSENCE_PERMIT = 'absence_permit';
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNPRESENT = 'unpresent';

    /**
     * Calculate the employee's daily state for one date.
     *
     * The result is derived from the existing HRWork tables.
     * No daily-state record is persisted.
     */
    public function getStatus(
        Employees $employee,
        Carbon|string $date
    ): array {
        return $this->getStatuses($employee, $date, $date)->first();
    }

    /**
     * Calculate daily states for an employee across an inclusive date range.
     *
     * Data sources are loaded once for the range to avoid querying every
     * source table once per date.
     */
    public function getStatuses(
        Employees $employee,
        Carbon|string $startDate,
        Carbon|string $endDate
    ): Collection {
        $start = $this->normalizeDate($startDate);
        $end = $this->normalizeDate($endDate);

        if ($start->gt($end)) {
            throw new InvalidArgumentException(
                'Start date tidak boleh lebih besar dari end date.'
            );
        }

        $contracts = $employee->employeeContract()
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $end->toDateString())
            ->where(function ($query) use ($start) {
                $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $start->toDateString());
            })
            ->orderByDesc('start_date')
            ->get();

        $workTimes = WorkTime::query()
            ->get()
            ->keyBy(fn($workTime) => strtolower(trim($workTime->day_of_week)));

        $holidays = Holidays::query()
            ->whereBetween('date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->get()
            ->keyBy(
                fn($holiday) => Carbon::parse($holiday->date)->toDateString()
            );

        $attendances = $employee->attendances()
            ->whereBetween('date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->get()
            ->keyBy(
                fn($attendance) => Carbon::parse($attendance->date)->toDateString()
            );

        $leaveRequests = $employee->leaveRequest()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get();

        $absenceRequests = $employee->employeeAbsenceRequest()
            ->where('status', 'approved')
            ->whereBetween('date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->get()
            ->keyBy(
                fn($absence) => Carbon::parse($absence->date)->toDateString()
            );

        $dayNames = [
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            7 => 'minggu',
        ];

        $today = now()->startOfDay();

        return collect(Carbon::period($start, $end))
            ->map(function (CarbonInterface $date) use (
                $employee,
                $contracts,
                $workTimes,
                $holidays,
                $attendances,
                $leaveRequests,
                $absenceRequests,
                $dayNames,
                $today
            ) {
                $date = Carbon::instance($date)->startOfDay();
                $dateKey = $date->toDateString();

                /*
                |--------------------------------------------------------------------------
                | CONTRACT
                |--------------------------------------------------------------------------
                */

                $contract = $contracts
                    ->filter(function ($contract) use ($date) {
                        $contractStart = Carbon::parse($contract->start_date);
                        $contractEnd = $contract->end_date
                            ? Carbon::parse($contract->end_date)
                            : null;

                        return $contractStart->lte($date)
                            && (!$contractEnd || $contractEnd->gte($date));
                    })
                    ->sortByDesc(
                        fn($contract) => Carbon::parse($contract->start_date)
                    )
                    ->first();

                if (!$contract) {
                    return $this->makeState(
                        employee: $employee,
                        date: $date,
                        status: self::STATUS_OUTSIDE_CONTRACT,
                        isWorkingDay: false,
                        isPaid: false,
                        isLate: false,
                        isUnpresent: false,
                        source: 'contract'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | WORK TIME
                |--------------------------------------------------------------------------
                */

                $dayName = $dayNames[$date->dayOfWeekIso];
                $workTime = $workTimes->get($dayName);

                if (!$workTime || !$workTime->is_working_day) {
                    return $this->makeState(
                        employee: $employee,
                        date: $date,
                        status: self::STATUS_NON_WORKING,
                        isWorkingDay: false,
                        isPaid: false,
                        isLate: false,
                        isUnpresent: false,
                        source: 'work_time',
                        sourceId: $workTime?->id,
                        contractId: $contract->id,
                        workTimeId: $workTime?->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | HOLIDAY
                |--------------------------------------------------------------------------
                |
                | Holiday takes the date out of the normal working calendar.
                | Attendance on a holiday remains available in the source table,
                | but the daily calendar state stays holiday.
                |
                */

                $holiday = $holidays->get($dateKey);

                if ($holiday) {
                    return $this->makeState(
                        employee: $employee,
                        date: $date,
                        status: self::STATUS_HOLIDAY,
                        isWorkingDay: false,
                        isPaid: false,
                        isLate: false,
                        isUnpresent: false,
                        source: 'holiday',
                        sourceId: $holiday->id,
                        contractId: $contract->id,
                        workTimeId: $workTime->id,
                        holidayId: $holiday->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | ATTENDANCE
                |--------------------------------------------------------------------------
                |
                | Attendance takes precedence over leave/absence so a real
                | check-in is not turned into an absent state accidentally.
                |
                */

                $attendance = $attendances->get($dateKey);

                if ($attendance && $attendance->check_in_at) {
                    $status = strtolower(trim((string) $attendance->status)) === 'late'
                        ? self::STATUS_LATE
                        : self::STATUS_PRESENT;

                    return $this->makeState(
                        employee: $employee,
                        date: $date,
                        status: $status,
                        isWorkingDay: true,
                        isPaid: true,
                        isLate: $status === self::STATUS_LATE,
                        isUnpresent: false,
                        source: 'attendance',
                        sourceId: $attendance->id,
                        contractId: $contract->id,
                        workTimeId: $workTime->id,
                        attendanceId: $attendance->id,
                        lateMinutes: (int) $attendance->late_minutes
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | APPROVED LEAVE
                |--------------------------------------------------------------------------
                */

                $leave = $leaveRequests->first(function ($request) use ($date) {
                    $leaveStart = Carbon::parse($request->start_date)->startOfDay();
                    $leaveEnd = Carbon::parse($request->end_date)->startOfDay();

                    return $date->betweenIncluded($leaveStart, $leaveEnd);
                });

                if ($leave) {
                    return $this->makeState(
                        employee: $employee,
                        date: $date,
                        status: self::STATUS_PAID_LEAVE,
                        isWorkingDay: true,
                        isPaid: true,
                        isLate: false,
                        isUnpresent: false,
                        source: 'leave',
                        sourceId: $leave->id,
                        contractId: $contract->id,
                        workTimeId: $workTime->id,
                        leaveRequestId: $leave->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | APPROVED ABSENCE: SAKIT / IZIN
                |--------------------------------------------------------------------------
                */

                $absence = $absenceRequests->get($dateKey);

                if ($absence) {
                    $status = $absence->type === 'sakit'
                        ? self::STATUS_ABSENCE_SICK
                        : self::STATUS_ABSENCE_PERMIT;

                    return $this->makeState(
                        employee: $employee,
                        date: $date,
                        status: $status,
                        isWorkingDay: true,
                        isPaid: false,
                        isLate: false,
                        isUnpresent: false,
                        source: 'absence',
                        sourceId: $absence->id,
                        contractId: $contract->id,
                        workTimeId: $workTime->id,
                        absenceRequestId: $absence->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | FUTURE DATE
                |--------------------------------------------------------------------------
                |
                | A future working day is not unpresent because the employee
                | has not had the opportunity to attend yet.
                |
                */

                if ($date->gte($today)) {
                    return $this->makeState(
                        employee: $employee,
                        date: $date,
                        status: self::STATUS_PENDING,
                        isWorkingDay: true,
                        isPaid: false,
                        isLate: false,
                        isUnpresent: false,
                        source: 'calculation',
                        contractId: $contract->id,
                        workTimeId: $workTime->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | UNPRESENT
                |--------------------------------------------------------------------------
                */

                return $this->makeState(
                    employee: $employee,
                    date: $date,
                    status: self::STATUS_UNPRESENT,
                    isWorkingDay: true,
                    isPaid: false,
                    isLate: false,
                    isUnpresent: true,
                    source: 'calculation',
                    contractId: $contract->id,
                    workTimeId: $workTime->id
                );
            })
            ->values();
    }

    private function makeState(
        Employees $employee,
        Carbon $date,
        string $status,
        bool $isWorkingDay,
        bool $isPaid,
        bool $isLate,
        bool $isUnpresent,
        string $source,
        ?int $sourceId = null,
        ?int $contractId = null,
        ?int $workTimeId = null,
        ?int $holidayId = null,
        ?int $attendanceId = null,
        ?int $leaveRequestId = null,
        ?int $absenceRequestId = null,
        int $lateMinutes = 0
    ): array {
        return [
            'employee_id' => $employee->id,
            'date' => $date->toDateString(),
            'status' => $status,

            'is_working_day' => $isWorkingDay,
            'is_paid' => $isPaid,
            'is_late' => $isLate,
            'is_unpresent' => $isUnpresent,

            'source' => $source,
            'source_id' => $sourceId,

            'contract_id' => $contractId,
            'work_time_id' => $workTimeId,
            'holiday_id' => $holidayId,
            'attendance_id' => $attendanceId,
            'leave_request_id' => $leaveRequestId,
            'absence_request_id' => $absenceRequestId,

            'late_minutes' => $lateMinutes,
        ];
    }

    private function normalizeDate(Carbon|string|CarbonInterface $date): Carbon
    {
        return $date instanceof CarbonInterface
            ? Carbon::instance($date)->startOfDay()
            : Carbon::parse($date)->startOfDay();
    }
}
