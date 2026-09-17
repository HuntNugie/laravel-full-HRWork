<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ModalApproveLeave extends Component
{
    public LeaveRequest $request;

    public function mount(LeaveRequest $request): void
    {
        $this->request = $request->load([
            'employees',
            'leaveType',
            'employeeContract',
        ]);
    }

    public function approve(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Refresh data terbaru
        |--------------------------------------------------------------------------
        */

        $this->request->refresh();

        /*
        |--------------------------------------------------------------------------
        | Pastikan request masih pending
        |--------------------------------------------------------------------------
        */

        if ($this->request->status !== 'pending') {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Tidak dapat diproses',
                message: 'Pengajuan cuti ini sudah tidak berstatus menunggu.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan employee masih ada
        |--------------------------------------------------------------------------
        */

        $employee = $this->request->employees;

        if (!$employee) {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Tidak dapat diproses',
                message: 'Data karyawan tidak ditemukan.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan contract masih aktif
        |--------------------------------------------------------------------------
        */

        $contract = $this->request->employeeContract;

        if (!$contract || $contract->status !== 'active') {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Tidak dapat diproses',
                message: 'Contract karyawan sudah tidak aktif.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil entitlement
        |--------------------------------------------------------------------------
        */

        $entitlement = $contract
            ->contractLeave()
            ->where(
                'leave_type_id',
                $this->request->leave_type_id
            )
            ->first();

        if (!$entitlement) {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Tidak dapat diproses',
                message: 'Jatah cuti tidak ditemukan pada contract karyawan.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $approved = false;

        DB::transaction(function () use (
            $employee,
            $contract,
            $entitlement,
            &$approved
        ) {

            /*
            |--------------------------------------------------------------------------
            | Ambil request terbaru dengan lock
            |--------------------------------------------------------------------------
            */

            $leaveRequest = LeaveRequest::query()
                ->whereKey($this->request->id)
                ->lockForUpdate()
                ->first();

            if (!$leaveRequest) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Cek kembali status
            |--------------------------------------------------------------------------
            */

            if ($leaveRequest->status !== 'pending') {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Hitung approved
            |--------------------------------------------------------------------------
            |
            | Request yang sedang diproses masih pending sehingga
            | otomatis tidak ikut dihitung.
            |
            */

            $usedDays = (int) $employee
                ->leaveRequest()
                ->where(
                    'leave_type_id',
                    $leaveRequest->leave_type_id
                )
                ->where(
                    'status',
                    'approved'
                )
                ->sum('total_days');


            /*
            |--------------------------------------------------------------------------
            | Hitung pending LAIN
            |--------------------------------------------------------------------------
            |
            | Request yang sedang diproses dikecualikan.
            |
            */

            $pendingDays = (int) $employee
                ->leaveRequest()
                ->where(
                    'leave_type_id',
                    $leaveRequest->leave_type_id
                )
                ->where(
                    'status',
                    'pending'
                )
                ->whereKeyNot($leaveRequest->id)
                ->sum('total_days');


            /*
            |--------------------------------------------------------------------------
            | Sisa yang tersedia untuk request ini
            |--------------------------------------------------------------------------
            */

            $availableDays = max(
                0,
                $entitlement->days
                    - $usedDays
                    - $pendingDays
            );


            /*
            |--------------------------------------------------------------------------
            | Cek jatah
            |--------------------------------------------------------------------------
            */

            if ($leaveRequest->total_days > $availableDays) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Approve
            |--------------------------------------------------------------------------
            */

            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $approved = true;
        });


        /*
        |--------------------------------------------------------------------------
        | Gagal karena jatah tidak mencukupi
        |--------------------------------------------------------------------------
        */

        if (!$approved) {

            /*
            | Ambil data terbaru untuk pesan toast.
            */

            $usedDays = (int) $employee
                ->leaveRequest()
                ->where(
                    'leave_type_id',
                    $this->request->leave_type_id
                )
                ->where(
                    'status',
                    'approved'
                )
                ->sum('total_days');


            $pendingDays = (int) $employee
                ->leaveRequest()
                ->where(
                    'leave_type_id',
                    $this->request->leave_type_id
                )
                ->where(
                    'status',
                    'pending'
                )
                ->whereKeyNot($this->request->id)
                ->sum('total_days');


            $availableDays = max(
                0,
                $entitlement->days
                    - $usedDays
                    - $pendingDays
            );


            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Jatah Cuti Tidak Mencukupi',
                message: "Pengajuan {$this->request->total_days} hari tidak dapat disetujui. Sisa jatah yang tersedia hanya {$availableDays} hari."
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Refresh request
        |--------------------------------------------------------------------------
        */

        $this->request->refresh();


        /*
        |--------------------------------------------------------------------------
        | Refresh halaman management
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'leave-management-refresh'
        );


        /*
        |--------------------------------------------------------------------------
        | Tutup modal
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-modal-close',
            name: 'approve-leave-' . $this->request->id
        );

        $this->dispatch(
            'wirekit-modal-close',
            name: 'management-leave-detail-' . $this->request->id
        );


        /*
        |--------------------------------------------------------------------------
        | Toast sukses
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Pengajuan Disetujui',
            message: 'Pengajuan cuti berhasil disetujui.'
        );
    }

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-approve-leave'
        );
    }
}
