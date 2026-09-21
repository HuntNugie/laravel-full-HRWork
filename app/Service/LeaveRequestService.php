<?php

namespace App\Service;

use App\Models\ContractLeaveEntitlements;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use LogicException;

class LeaveRequestService
{
    public function currentActiveContract(Employees $employee, CarbonInterface|string|null $date = null): ?EmployeeContract
    {
        $targetDate = $date instanceof CarbonInterface
            ? Carbon::instance($date)->startOfDay()
            : Carbon::parse($date ?? today())->startOfDay();

        return $employee->employeeContract()
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $targetDate->toDateString())
            ->where(function ($query) use ($targetDate) {
                $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $targetDate->toDateString());
            })
            ->orderByDesc('start_date')
            ->first();
    }

    public function usedDays(ContractLeaveEntitlements $entitlement, int $year): int
    {
        return (int) LeaveRequest::query()
            ->where('employee_contract_id', $entitlement->employee_contract_id)
            ->where('leave_type_id', $entitlement->leave_type_id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('total_days');
    }

    public function pendingDays(ContractLeaveEntitlements $entitlement, int $year): int
    {
        return (int) LeaveRequest::query()
            ->where('employee_contract_id', $entitlement->employee_contract_id)
            ->where('leave_type_id', $entitlement->leave_type_id)
            ->where('status', 'pending')
            ->whereYear('start_date', $year)
            ->sum('total_days');
    }

    public function remainingDays(ContractLeaveEntitlements $entitlement, int $year): int
    {
        return max(
            0,
            (int) $entitlement->days
                - $this->usedDays($entitlement, $year)
                - $this->pendingDays($entitlement, $year)
        );
    }

    public function createPending(
        Employees $employee,
        int $leaveTypeId,
        CarbonInterface|string $startDate,
        CarbonInterface|string $endDate,
        string $reason,
    ): LeaveRequest {
        $start = $this->normalizeDate($startDate);
        $end = $this->normalizeDate($endDate);
        $reason = trim($reason);

        if ($start->gt($end)) {
            throw new LogicException('Tanggal selesai cuti tidak valid.');
        }

        if ($start->year !== $end->year) {
            throw new LogicException('Pengajuan cuti tidak boleh melewati pergantian tahun.');
        }

        if ($reason === '') {
            throw new LogicException('Alasan pengajuan cuti wajib diisi.');
        }

        return DB::transaction(function () use (
            $employee,
            $leaveTypeId,
            $start,
            $end,
            $reason,
        ) {
            $contract = $this->resolveContractForPeriod(
                employee: $employee,
                start: $start,
                end: $end,
            );

            if (!$contract) {
                throw new LogicException(
                    'Pengajuan cuti harus berada dalam periode contract aktif.'
                );
            }

            $contract = EmployeeContract::query()
                ->whereKey($contract->id)
                ->lockForUpdate()
                ->firstOrFail();

            $entitlement = $contract->contractLeave()
                ->where('leave_type_id', $leaveTypeId)
                ->lockForUpdate()
                ->first();

            if (!$entitlement) {
                throw new LogicException(
                    'Jenis cuti tidak tersedia pada contract aktif Anda.'
                );
            }

            $totalDays = $start->diffInDays($end) + 1;
            $leaveYear = $start->year;

            $availableDays = $this->remainingDays($entitlement, $leaveYear);

            if ($totalDays > $availableDays) {
                throw new LogicException(
                    "Jatah cuti yang tersedia untuk tahun {$leaveYear} hanya {$availableDays} hari."
                );
            }

            $hasLeaveOverlap = LeaveRequest::query()
                ->where('employee_id', $employee->id)
                ->whereIn('status', ['pending', 'approved'])
                ->whereDate('start_date', '<=', $end->toDateString())
                ->whereDate('end_date', '>=', $start->toDateString())
                ->exists();

            if ($hasLeaveOverlap) {
                throw new LogicException(
                    'Periode cuti bertabrakan dengan pengajuan cuti Anda yang masih aktif.'
                );
            }

            $hasAbsenceOverlap = $employee->employeeAbsenceRequest()
                ->whereIn('status', ['pending', 'approved'])
                ->whereBetween('date', [
                    $start->toDateString(),
                    $end->toDateString(),
                ])
                ->exists();

            if ($hasAbsenceOverlap) {
                throw new LogicException(
                    'Periode cuti bertabrakan dengan pengajuan sakit/izin yang masih aktif.'
                );
            }

            $hasAttendance = $employee->attendances()
                ->whereBetween('date', [
                    $start->toDateString(),
                    $end->toDateString(),
                ])
                ->whereNotNull('check_in_at')
                ->exists();

            if ($hasAttendance) {
                throw new LogicException(
                    'Periode cuti tidak dapat diajukan karena terdapat presensi pada salah satu tanggal.'
                );
            }

            return $employee->leaveRequest()->create([
                'employee_contract_id' => $contract->id,
                'leave_type_id' => $leaveTypeId,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'total_days' => $totalDays,
                'reason' => $reason,
                'status' => 'pending',
            ]);
        });
    }

    public function approve(LeaveRequest $request, int $approvedBy): LeaveRequest
    {
        return DB::transaction(function () use ($request, $approvedBy) {
            $leaveRequest = LeaveRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->first();

            if (!$leaveRequest) {
                throw new LogicException('Pengajuan cuti tidak ditemukan.');
            }

            if ($leaveRequest->status !== 'pending') {
                throw new LogicException('Pengajuan cuti ini sudah tidak berstatus menunggu.');
            }

            $employee = $leaveRequest->employees;
            $contract = $leaveRequest->employeeContract;

            if (!$employee) {
                throw new LogicException('Data karyawan tidak ditemukan.');
            }

            if (!$contract || $contract->status !== 'active') {
                throw new LogicException('Contract karyawan sudah tidak aktif.');
            }

            $start = $this->normalizeDate($leaveRequest->start_date);
            $end = $this->normalizeDate($leaveRequest->end_date);

            if ($start->year !== $end->year) {
                throw new LogicException('Pengajuan cuti tidak boleh melewati pergantian tahun.');
            }

            $contractStart = $this->normalizeDate($contract->start_date);
            $contractEnd = $contract->end_date
                ? $this->normalizeDate($contract->end_date)
                : null;

            if (
                $start->lt($contractStart)
                || ($contractEnd && $end->gt($contractEnd))
            ) {
                throw new LogicException(
                    'Periode cuti berada di luar periode contract karyawan.'
                );
            }

            $entitlement = $contract->contractLeave()
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->lockForUpdate()
                ->first();

            if (!$entitlement) {
                throw new LogicException(
                    'Jenis cuti tidak tersedia pada contract pengajuan.'
                );
            }

            $availableDays = $this->remainingDays(
                entitlement: $entitlement,
                year: $start->year,
            );

            if ((int) $leaveRequest->total_days > $availableDays) {
                throw new LogicException(
                    "Pengajuan {$leaveRequest->total_days} hari tidak dapat disetujui. Sisa jatah yang tersedia hanya {$availableDays} hari."
                );
            }

            $hasAttendance = $employee->attendances()
                ->whereBetween('date', [
                    $start->toDateString(),
                    $end->toDateString(),
                ])
                ->whereNotNull('check_in_at')
                ->exists();

            if ($hasAttendance) {
                throw new LogicException(
                    'Pengajuan cuti tidak dapat disetujui karena terdapat presensi pada salah satu tanggal.'
                );
            }

            $hasOtherAbsence = $employee->employeeAbsenceRequest()
                ->whereIn('status', ['pending', 'approved'])
                ->whereBetween('date', [
                    $start->toDateString(),
                    $end->toDateString(),
                ])
                ->exists();

            if ($hasOtherAbsence) {
                throw new LogicException(
                    'Pengajuan cuti tidak dapat disetujui karena ada pengajuan sakit/izin yang masih aktif.'
                );
            }

            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);

            return $leaveRequest->refresh();
        });
    }

    public function reject(LeaveRequest $request, int $rejectedBy, string $reason): LeaveRequest
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw new LogicException('Alasan penolakan wajib diisi.');
        }

        return DB::transaction(function () use ($request, $rejectedBy, $reason) {
            $leaveRequest = LeaveRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->first();

            if (!$leaveRequest) {
                throw new LogicException('Pengajuan cuti tidak ditemukan.');
            }

            if ($leaveRequest->status !== 'pending') {
                throw new LogicException('Pengajuan cuti ini sudah tidak berstatus menunggu.');
            }

            $leaveRequest->update([
                'status' => 'rejected',
                'rejected_by' => $rejectedBy,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            return $leaveRequest->refresh();
        });
    }

    public function cancel(LeaveRequest $request): LeaveRequest
    {
        return DB::transaction(function () use ($request) {
            $leaveRequest = LeaveRequest::query()
                ->whereKey($request->id)
                ->lockForUpdate()
                ->first();

            if (!$leaveRequest) {
                throw new LogicException('Pengajuan cuti tidak ditemukan.');
            }

            if (!in_array($leaveRequest->status, ['pending', 'approved'], true)) {
                throw new LogicException('Pengajuan cuti ini tidak dapat dibatalkan.');
            }

            $leaveRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return $leaveRequest->refresh();
        });
    }

    private function resolveContractForPeriod(
        Employees $employee,
        Carbon $start,
        Carbon $end,
    ): ?EmployeeContract {
        return $employee->employeeContract()
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $start->toDateString())
            ->where(function ($query) use ($end) {
                $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $end->toDateString());
            })
            ->orderByDesc('start_date')
            ->first();
    }

    private function normalizeDate(CarbonInterface|string $date): Carbon
    {
        return $date instanceof CarbonInterface
            ? Carbon::instance($date)->startOfDay()
            : Carbon::parse($date)->startOfDay();
    }
}
