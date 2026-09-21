<?php

namespace App\Service;

use App\Models\EmployeeAbsenceRequest;
use App\Models\Employees;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use LogicException;

class AbsenceRequestService
{
    public function create(
        Employees $employee,
        string $type,
        string $reason,
        CarbonInterface|string|null $date = null,
    ): EmployeeAbsenceRequest {
        $type = strtolower(trim($type));
        $reason = trim($reason);
        $targetDate = $this->normalizeDate($date ?? today());

        if (!in_array($type, ['sakit', 'izin'], true)) {
            throw new LogicException('Jenis pengajuan harus berupa sakit atau izin.');
        }

        if ($reason === '') {
            throw new LogicException('Alasan pengajuan sakit/izin wajib diisi.');
        }

        return DB::transaction(function () use (
            $employee,
            $type,
            $reason,
            $targetDate,
        ) {
            $existing = $employee->employeeAbsenceRequest()
                ->whereDate('date', $targetDate->toDateString())
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new LogicException(
                    'Anda sudah mengajukan sakit/izin untuk tanggal tersebut.'
                );
            }

            $hasAttendance = $employee->attendances()
                ->whereDate('date', $targetDate->toDateString())
                ->whereNotNull('check_in_at')
                ->exists();

            if ($hasAttendance) {
                throw new LogicException(
                    'Pengajuan sakit/izin tidak dapat dibuat karena sudah terdapat presensi pada tanggal tersebut.'
                );
            }

            $hasLeave = $employee->leaveRequest()
                ->whereIn('status', ['pending', 'approved'])
                ->whereDate('start_date', '<=', $targetDate->toDateString())
                ->whereDate('end_date', '>=', $targetDate->toDateString())
                ->exists();

            if ($hasLeave) {
                throw new LogicException(
                    'Tanggal tersebut sudah memiliki pengajuan cuti yang masih aktif.'
                );
            }

            return $employee->employeeAbsenceRequest()->create([
                'date' => $targetDate->toDateString(),
                'type' => $type,
                'reason' => $reason,
                'status' => 'pending',
            ]);
        });
    }

    public function ensureNoRequestForCheckIn(
        Employees $employee,
        CarbonInterface|string $date,
    ): void {
        $targetDate = $this->normalizeDate($date);

        $hasRequest = $employee->employeeAbsenceRequest()
            ->whereDate('date', $targetDate->toDateString())
            ->exists();

        if ($hasRequest) {
            throw new LogicException(
                'Anda sudah mengajukan sakit/izin untuk hari ini sehingga tidak dapat melakukan check in.'
            );
        }
    }

    public function approve(
        EmployeeAbsenceRequest $request,
        int $approvedBy,
    ): EmployeeAbsenceRequest {
        return DB::transaction(function () use ($request, $approvedBy) {
            $absence = EmployeeAbsenceRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->first();

            if (!$absence) {
                throw new LogicException('Pengajuan sakit/izin tidak ditemukan.');
            }

            if ($absence->status !== 'pending') {
                throw new LogicException(
                    'Pengajuan sakit/izin ini sudah tidak berstatus menunggu.'
                );
            }

            $employee = $absence->employees;

            if (!$employee) {
                throw new LogicException('Data karyawan tidak ditemukan.');
            }

            $hasAttendance = $employee->attendances()
                ->whereDate('date', $absence->date->toDateString())
                ->whereNotNull('check_in_at')
                ->exists();

            if ($hasAttendance) {
                throw new LogicException(
                    'Pengajuan sakit/izin tidak dapat disetujui karena karyawan sudah melakukan presensi.'
                );
            }

            $hasLeave = $employee->leaveRequest()
                ->whereIn('status', ['pending', 'approved'])
                ->whereDate('start_date', '<=', $absence->date->toDateString())
                ->whereDate('end_date', '>=', $absence->date->toDateString())
                ->exists();

            if ($hasLeave) {
                throw new LogicException(
                    'Pengajuan sakit/izin tidak dapat disetujui karena tanggal tersebut memiliki pengajuan cuti aktif.'
                );
            }

            $absence->update([
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);

            return $absence->refresh();
        });
    }

    public function reject(
        EmployeeAbsenceRequest $request,
        int $rejectedBy,
    ): EmployeeAbsenceRequest {
        return DB::transaction(function () use ($request, $rejectedBy) {
            $absence = EmployeeAbsenceRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->first();

            if (!$absence) {
                throw new LogicException('Pengajuan sakit/izin tidak ditemukan.');
            }

            if ($absence->status !== 'pending') {
                throw new LogicException(
                    'Pengajuan sakit/izin ini sudah tidak berstatus menunggu.'
                );
            }

            $absence->update([
                'status' => 'rejected',
                'approved_by' => $rejectedBy,
                'approved_at' => now(),
            ]);

            return $absence->refresh();
        });
    }

    private function normalizeDate(CarbonInterface|string $date): Carbon
    {
        return $date instanceof CarbonInterface
            ? Carbon::instance($date)->startOfDay()
            : Carbon::parse($date)->startOfDay();
    }
}
