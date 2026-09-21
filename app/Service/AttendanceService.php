<?php

namespace App\Service;

use App\Models\AttedanceSetting;
use App\Models\Attendances;
use App\Models\Employees;
use App\Models\WorkTime;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use LogicException;

class AttendanceService
{
    public function __construct(
        private readonly EmployeeDailyStatusService $dailyStatusService,
        private readonly AbsenceRequestService $absenceRequestService,
    ) {
    }

    /**
     * Record employee check-in using the current Daily Status rules.
     */
    public function checkIn(
        Employees $employee,
        float $latitude,
        float $longitude,
        CarbonInterface|string|null $now = null,
    ): Attendances {
        $checkIn = $this->normalizeDateTime($now);

        $this->validateLocation($latitude, $longitude);

        $this->absenceRequestService->ensureNoRequestForCheckIn(
            employee: $employee,
            date: $checkIn,
        );

        $dailyStatus = $this->dailyStatusService->getStatus(
            employee: $employee,
            date: $checkIn->toDateString(),
        );

        $this->ensureCheckInAllowed($dailyStatus, $checkIn);

        return DB::transaction(function () use (
            $employee,
            $checkIn,
            $latitude,
            $longitude,
        ) {
            $attendance = Attendances::query()
                ->where('employee_id', $employee->id)
                ->whereDate('date', $checkIn->toDateString())
                ->lockForUpdate()
                ->first();

            if ($attendance?->check_in_at) {
                throw new LogicException(
                    'Anda sudah melakukan check in untuk hari ini.'
                );
            }

            $dailyStatus = $this->dailyStatusService->getStatus(
                employee: $employee,
                date: $checkIn->toDateString(),
            );

            $workTime = WorkTime::query()
                ->find($dailyStatus['work_time_id']);

            if (!$workTime) {
                throw new LogicException(
                    'Jadwal kerja hari ini tidak ditemukan.'
                );
            }

            $startTime = $checkIn->copy()
                ->startOfDay()
                ->setTimeFromTimeString($workTime->start_time);

            $settings = AttedanceSetting::query()->first();

            if (!$settings) {
                throw new LogicException(
                    'Pengaturan presensi belum tersedia.'
                );
            }

            $lateLimit = $startTime->copy()
                ->addMinutes((int) $settings->late_tolerance_minutes);

            $isLate = $checkIn->greaterThan($lateLimit);
            $lateMinutes = $isLate
                ? $startTime->diffInMinutes($checkIn)
                : 0;

            return $employee->attendances()->create([
                'date' => $checkIn->toDateString(),
                'check_in_at' => $checkIn,
                'check_in_latitude' => $latitude,
                'check_in_longitude' => $longitude,
                'status' => $isLate ? 'late' : 'present',
                'late_minutes' => $lateMinutes,
            ]);
        });
    }

    /**
     * Record employee check-out and calculate work duration in minutes.
     */
    public function checkOut(
        Employees $employee,
        float $latitude,
        float $longitude,
        CarbonInterface|string|null $now = null,
    ): Attendances {
        $checkOut = $this->normalizeDateTime($now);

        $this->validateLocation($latitude, $longitude);

        return DB::transaction(function () use (
            $employee,
            $checkOut,
            $latitude,
            $longitude,
        ) {
            $attendance = Attendances::query()
                ->where('employee_id', $employee->id)
                ->whereDate('date', $checkOut->toDateString())
                ->lockForUpdate()
                ->first();

            if (!$attendance || !$attendance->check_in_at) {
                throw new LogicException(
                    'Anda belum melakukan check in untuk hari ini.'
                );
            }

            if ($attendance->check_out_at) {
                throw new LogicException(
                    'Anda sudah melakukan check out untuk hari ini.'
                );
            }

            $checkIn = Carbon::instance($attendance->check_in_at);

            if ($checkOut->lt($checkIn)) {
                throw new LogicException(
                    'Waktu check out tidak boleh lebih awal dari waktu check in.'
                );
            }

            $workDuration = $checkIn->diffInMinutes($checkOut);

            $attendance->update([
                'check_out_at' => $checkOut,
                'check_out_latitude' => $latitude,
                'check_out_longitude' => $longitude,
                'work_duration' => $workDuration,
            ]);

            return $attendance->refresh();
        });
    }

    private function ensureCheckInAllowed(
        array $dailyStatus,
        CarbonInterface $checkIn,
    ): void {
        $blockedStatuses = [
            EmployeeDailyStatusService::STATUS_OUTSIDE_CONTRACT =>
                'Anda belum berada dalam periode kontrak.',
            EmployeeDailyStatusService::STATUS_NON_WORKING =>
                'Tidak ada jadwal kerja hari ini.',
            EmployeeDailyStatusService::STATUS_HOLIDAY =>
                'Hari ini merupakan hari libur.',
            EmployeeDailyStatusService::STATUS_PAID_LEAVE =>
                'Anda sedang dalam cuti yang disetujui.',
            EmployeeDailyStatusService::STATUS_ABSENCE_SICK =>
                'Anda memiliki izin sakit yang disetujui untuk hari ini.',
            EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT =>
                'Anda memiliki izin yang disetujui untuk hari ini.',
        ];

        $status = $dailyStatus['status'];

        if (isset($blockedStatuses[$status])) {
            throw new LogicException($blockedStatuses[$status]);
        }

        if (in_array($status, [
            EmployeeDailyStatusService::STATUS_PRESENT,
            EmployeeDailyStatusService::STATUS_LATE,
        ], true)) {
            throw new LogicException(
                'Anda sudah melakukan check in untuk hari ini.'
            );
        }

        $workTimeId = $dailyStatus['work_time_id'];

        $workTime = WorkTime::query()->find($workTimeId);

        if (!$workTime) {
            throw new LogicException(
                'Jadwal kerja hari ini tidak ditemukan.'
            );
        }

        $startTime = $checkIn->copy()
            ->startOfDay()
            ->setTimeFromTimeString($workTime->start_time);

        $endTime = $checkIn->copy()
            ->startOfDay()
            ->setTimeFromTimeString($workTime->end_time);

        if ($checkIn->lt($startTime)) {
            throw new LogicException(
                'Presensi baru dapat dilakukan mulai jam ' .
                $startTime->format('H:i')
                . '.'
            );
        }

        if ($checkIn->gt($endTime)) {
            throw new LogicException(
                'Presensi masuk untuk hari ini sudah ditutup setelah jam ' .
                $endTime->format('H:i')
                . '.'
            );
        }
    }

    private function validateLocation(
        float $latitude,
        float $longitude,
    ): void {
        if (
            !is_finite($latitude)
            || !is_finite($longitude)
            || $latitude < -90
            || $latitude > 90
            || $longitude < -180
            || $longitude > 180
        ) {
            throw new LogicException(
                'Koordinat lokasi tidak valid.'
            );
        }
    }

    private function normalizeDateTime(
        CarbonInterface|string|null $dateTime,
    ): Carbon {
        return $dateTime instanceof CarbonInterface
            ? Carbon::instance($dateTime)
            : ($dateTime
                ? Carbon::parse($dateTime)
                : now());
    }
}
