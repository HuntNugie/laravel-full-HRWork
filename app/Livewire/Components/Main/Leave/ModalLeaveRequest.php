<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\ContractLeaveEntitlements;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ModalLeaveRequest extends Component
{
    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public ?int $leaveTypeId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public int $totalDays = 0;

    public ?string $reason = null;


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public Collection $entitlements;


    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $employee = Auth::user()->employees;

        $contract = $employee?->latestEmployeeContract;

        $this->entitlements = $contract
            ? $contract
            ->contractLeave()
            ->with('leaveType')
            ->get()
            : new Collection();
    }


    /*
    |--------------------------------------------------------------------------
    | SELECTED ENTITLEMENT
    |--------------------------------------------------------------------------
    */

    public function getSelectedEntitlementProperty(): ?ContractLeaveEntitlements
    {
        if (!$this->leaveTypeId) {
            return null;
        }

        return $this->entitlements->firstWhere(
            'leave_type_id',
            $this->leaveTypeId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVED DAYS
    |--------------------------------------------------------------------------
    |
    | Hanya menghitung cuti approved pada tahun berjalan.
    |
    */

    public function usedLeave(int $leaveTypeId): int
    {
        $employee = Auth::user()->employees;

        if (!$employee) {
            return 0;
        }

        return (int) $employee
            ->leaveRequest()
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');
    }


    /*
    |--------------------------------------------------------------------------
    | PENDING DAYS
    |--------------------------------------------------------------------------
    |
    | Pending dianggap sebagai jatah yang sedang dipesan.
    | Hanya pending pada tahun berjalan yang dihitung.
    |
    */

    public function pendingLeave(int $leaveTypeId): int
    {
        $employee = Auth::user()->employees;

        if (!$employee) {
            return 0;
        }

        return (int) $employee
            ->leaveRequest()
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'pending')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');
    }


    /*
    |--------------------------------------------------------------------------
    | AVAILABLE DAYS
    |--------------------------------------------------------------------------
    |
    | Sisa tersedia:
    |
    | Jatah
    | - Approved tahun berjalan
    | - Pending tahun berjalan
    |
    */

    public function remainingLeave(
        ContractLeaveEntitlements $entitlement
    ): int {
        $used = $this->usedLeave(
            $entitlement->leave_type_id
        );

        $pending = $this->pendingLeave(
            $entitlement->leave_type_id
        );

        return max(
            0,
            $entitlement->days - $used - $pending
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE TANGGAL
    |--------------------------------------------------------------------------
    */

    public function updatedStartDate(): void
    {
        $this->calculateTotalDays();
    }

    public function updatedEndDate(): void
    {
        $this->calculateTotalDays();
    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG TOTAL HARI
    |--------------------------------------------------------------------------
    */

    private function calculateTotalDays(): void
    {
        $this->totalDays = 0;

        if (!$this->startDate || !$this->endDate) {
            return;
        }

        try {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
        } catch (\Throwable) {
            return;
        }

        if ($end->lt($start)) {
            return;
        }

        $this->totalDays = $start->diffInDays($end) + 1;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI TAHUN
    |--------------------------------------------------------------------------
    |
    | Satu pengajuan cuti tidak boleh melewati tahun kalender.
    |
    | Contoh:
    | 29 Des 2026 - 02 Jan 2027 ❌
    |
    */

    private function validateLeaveYear(): bool
    {
        if (!$this->startDate || !$this->endDate) {
            return false;
        }

        try {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
        } catch (\Throwable) {
            return false;
        }

        if ($start->year === $end->year) {
            return true;
        }

        $this->dispatch(
            'wirekit-toast',
            variant: 'danger',
            title: 'Periode Tidak Valid',
            message: 'Pengajuan cuti tidak boleh melewati pergantian tahun.'
        );

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    public function submit(): void
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI FORM
        |--------------------------------------------------------------------------
        */

        $this->validate([
            'leaveTypeId' => [
                'required',
                'integer',
            ],

            'startDate' => [
                'required',
                'date',
            ],

            'endDate' => [
                'required',
                'date',
                'after_or_equal:startDate',
            ],

            'reason' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ], [
            'leaveTypeId.required' =>
            'Silakan pilih jenis cuti.',

            'leaveTypeId.integer' =>
            'Jenis cuti tidak valid.',

            'startDate.required' =>
            'Tanggal mulai wajib diisi.',

            'startDate.date' =>
            'Tanggal mulai tidak valid.',

            'endDate.required' =>
            'Tanggal selesai wajib diisi.',

            'endDate.date' =>
            'Tanggal selesai tidak valid.',

            'endDate.after_or_equal' =>
            'Tanggal selesai harus sama atau setelah tanggal mulai.',

            'reason.required' =>
            'Alasan pengajuan cuti wajib diisi.',

            'reason.min' =>
            'Alasan pengajuan minimal 5 karakter.',

            'reason.max' =>
            'Alasan pengajuan maksimal 1000 karakter.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | VALIDASI TAHUN
        |--------------------------------------------------------------------------
        */

        if (!$this->validateLeaveYear()) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE
        |--------------------------------------------------------------------------
        */

        $employee = Auth::user()->employees;

        if (!$employee) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Data karyawan tidak ditemukan.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CONTRACT AKTIF
        |--------------------------------------------------------------------------
        */

        $contract = $employee->latestEmployeeContract;

        if (!$contract || $contract->status !== 'active') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Anda tidak memiliki contract aktif.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ENTITLEMENT
        |--------------------------------------------------------------------------
        */

        $entitlement = $contract
            ->contractLeave()
            ->where('leave_type_id', $this->leaveTypeId)
            ->with('leaveType')
            ->first();

        if (!$entitlement) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Jenis cuti tidak tersedia pada contract aktif Anda.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG DURASI
        |--------------------------------------------------------------------------
        */

        $this->calculateTotalDays();

        if ($this->totalDays <= 0) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Durasi Tidak Valid',
                message: 'Silakan periksa kembali tanggal mulai dan tanggal selesai.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | TAHUN CUTI
        |--------------------------------------------------------------------------
        */

        $leaveYear = Carbon::parse($this->startDate)->year;


        /*
        |--------------------------------------------------------------------------
        | CEK JATAH TAHUN BERJALAN
        |--------------------------------------------------------------------------
        |
        | Approved + Pending pada tahun pengajuan.
        |
        */

        $usedDays = (int) $employee
            ->leaveRequest()
            ->where('leave_type_id', $entitlement->leave_type_id)
            ->where('status', 'approved')
            ->whereYear('start_date', $leaveYear)
            ->sum('total_days');

        $pendingDays = (int) $employee
            ->leaveRequest()
            ->where('leave_type_id', $entitlement->leave_type_id)
            ->where('status', 'pending')
            ->whereYear('start_date', $leaveYear)
            ->sum('total_days');

        $availableDays = max(
            0,
            $entitlement->days - $usedDays - $pendingDays
        );


        /*
        |--------------------------------------------------------------------------
        | JATAH SUDAH HABIS
        |--------------------------------------------------------------------------
        */

        if ($availableDays <= 0) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Jatah Cuti Habis',
                message: "Jatah cuti {$entitlement->leaveType?->name} untuk tahun {$leaveYear} sudah habis."
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | PENGAJUAN MELEBIHI JATAH
        |--------------------------------------------------------------------------
        */

        if ($this->totalDays > $availableDays) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Jatah Cuti Tidak Mencukupi',
                message: "Jatah cuti yang tersedia untuk tahun {$leaveYear} hanya {$availableDays} hari."
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CEK BENTROK PERIODE
        |--------------------------------------------------------------------------
        |
        | Pending dan approved dianggap sebagai periode aktif.
        |
        */

        $hasOverlap = $employee
            ->leaveRequest()
            ->whereIn('status', [
                'pending',
                'approved',
            ])
            ->whereDate(
                'start_date',
                '<=',
                $this->endDate
            )
            ->whereDate(
                'end_date',
                '>=',
                $this->startDate
            )
            ->exists();

        if ($hasOverlap) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Periode Bertabrakan',
                message: 'Periode cuti bertabrakan dengan pengajuan cuti Anda yang masih aktif.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        |
        | Jatah tidak dipotong.
        | Hanya membuat pengajuan pending.
        |
        */

        DB::transaction(function () use (
            $employee,
            $contract,
            $entitlement
        ) {
            LeaveRequest::create([
                'employee_id' => $employee->id,
                'employee_contract_id' => $contract->id,
                'leave_type_id' => $entitlement->leave_type_id,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'total_days' => $this->totalDays,
                'reason' => $this->reason,
                'status' => 'pending',
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | REFRESH ENTITLEMENT
        |--------------------------------------------------------------------------
        */

        $this->entitlements = $contract
            ->contractLeave()
            ->with('leaveType')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RESET FORM
        |--------------------------------------------------------------------------
        */

        $this->resetForm();


        /*
        |--------------------------------------------------------------------------
        | BERITAHU PARENT
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'leave-request'
        );


        /*
        |--------------------------------------------------------------------------
        | TUTUP MODAL
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-modal-close',
            name: 'create-leave-request'
        );


        /*
        |--------------------------------------------------------------------------
        | TOAST BERHASIL
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESET FORM
    |--------------------------------------------------------------------------
    */

    public function resetForm(): void
    {
        $this->reset([
            'leaveTypeId',
            'startDate',
            'endDate',
            'totalDays',
            'reason',
        ]);

        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-leave-request'
        );
    }
}
