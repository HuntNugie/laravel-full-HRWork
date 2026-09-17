<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\ContractLeaveEntitlements;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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
    | CUTI TERPAKAI
    |--------------------------------------------------------------------------
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
            ->sum('total_days');
    }


    /*
    |--------------------------------------------------------------------------
    | SISA CUTI
    |--------------------------------------------------------------------------
    */

    public function remainingLeave(
        ContractLeaveEntitlements $entitlement
    ): int {
        $used = $this->usedLeave(
            $entitlement->leave_type_id
        );

        return max(
            0,
            $entitlement->days - $used
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
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    public function submit(): void
    {
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
        | EMPLOYEE
        |--------------------------------------------------------------------------
        */

        $employee = Auth::user()->employees;

        if (!$employee) {
            $this->addError(
                'leaveTypeId',
                'Data karyawan tidak ditemukan.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CONTRACT
        |--------------------------------------------------------------------------
        */

        $contract = $employee->latestEmployeeContract;

        if (!$contract || $contract->status !== 'active') {
            $this->addError(
                'leaveTypeId',
                'Anda tidak memiliki contract aktif.'
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
            $this->addError(
                'leaveTypeId',
                'Jenis cuti tidak tersedia pada contract aktif Anda.'
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
            $this->addError(
                'startDate',
                'Durasi cuti tidak valid.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CEK SISA CUTI
        |--------------------------------------------------------------------------
        */

        $usedDays = $this->usedLeave(
            $entitlement->leave_type_id
        );

        $remainingDays = max(
            0,
            $entitlement->days - $usedDays
        );

        if ($this->totalDays > $remainingDays) {
            $this->addError(
                'endDate',
                "Sisa cuti Anda hanya {$remainingDays} hari."
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CEK BENTROK PERIODE
        |--------------------------------------------------------------------------
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
            $this->addError(
                'startDate',
                'Periode cuti bertabrakan dengan pengajuan cuti Anda sebelumnya.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | RESET
        |--------------------------------------------------------------------------
        */

        $this->resetForm();


        /*
        |--------------------------------------------------------------------------
        | BERITAHU PARENT
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'leave-request-created'
        );

        $this->dispatch('wirekit-modal-close', name: 'create-leave-request');


        /*
        |--------------------------------------------------------------------------
        | TOAST
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
