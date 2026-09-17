<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use Carbon\Carbon;
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
        | Tahun Cuti
        |--------------------------------------------------------------------------
        |
        | Jatah dihitung berdasarkan tahun dari tanggal mulai cuti.
        |
        */

        try {
            $leaveYear = Carbon::parse(
                $this->request->start_date
            )->year;
        } catch (\Throwable) {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Tanggal Tidak Valid',
                message: 'Tanggal mulai pengajuan cuti tidak valid.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan pengajuan tidak melewati tahun
        |--------------------------------------------------------------------------
        */

        try {
            $startDate = Carbon::parse(
                $this->request->start_date
            );

            $endDate = Carbon::parse(
                $this->request->end_date
            );
        } catch (\Throwable) {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Tanggal Tidak Valid',
                message: 'Periode pengajuan cuti tidak valid.'
            );

            return;
        }

        if ($startDate->year !== $endDate->year) {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Periode Tidak Valid',
                message: 'Pengajuan cuti tidak boleh melewati pergantian tahun.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $approved = false;

        $availableDays = 0;

        $entitlementDays = 0;

        $usedDays = 0;

        $pendingDays = 0;


        DB::transaction(function () use (
            $employee,
            $contract,
            $leaveYear,
            &$approved,
            &$availableDays,
            &$entitlementDays,
            &$usedDays,
            &$pendingDays,
        ) {

            /*
            |--------------------------------------------------------------------------
            | Ambil request terbaru + lock
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
            | Cek status kembali
            |--------------------------------------------------------------------------
            */

            if ($leaveRequest->status !== 'pending') {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Ambil entitlement + lock
            |--------------------------------------------------------------------------
            |
            | Lock entitlement agar approval cuti yang menggunakan
            | entitlement yang sama berjalan secara berurutan.
            |
            */

            $entitlement = $contract
                ->contractLeave()
                ->where(
                    'leave_type_id',
                    $leaveRequest->leave_type_id
                )
                ->lockForUpdate()
                ->first();

            if (!$entitlement) {
                return;
            }


            $entitlementDays = (int) $entitlement->days;


            /*
            |--------------------------------------------------------------------------
            | Hitung approved tahun pengajuan
            |--------------------------------------------------------------------------
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
                ->whereYear(
                    'start_date',
                    $leaveYear
                )
                ->sum('total_days');


            /*
            |--------------------------------------------------------------------------
            | Hitung pending lain pada tahun pengajuan
            |--------------------------------------------------------------------------
            |
            | Request yang sedang di-approve dikecualikan.
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
                ->whereKeyNot(
                    $leaveRequest->id
                )
                ->whereYear(
                    'start_date',
                    $leaveYear
                )
                ->sum('total_days');


            /*
            |--------------------------------------------------------------------------
            | Hitung jatah yang tersedia untuk request ini
            |--------------------------------------------------------------------------
            */

            $availableDays = max(
                0,
                $entitlementDays
                    - $usedDays
                    - $pendingDays
            );


            /*
            |--------------------------------------------------------------------------
            | Cek apakah jatah cukup
            |--------------------------------------------------------------------------
            */

            if (
                (int) $leaveRequest->total_days
                > $availableDays
            ) {
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
        | Request gagal di-approve
        |--------------------------------------------------------------------------
        */

        if (!$approved) {

            if ($entitlementDays <= 0) {

                $this->dispatch(
                    'wirekit-toast',
                    variant: 'danger',
                    title: 'Jatah Cuti Tidak Tersedia',
                    message: 'Jatah cuti pada contract karyawan tidak tersedia.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Request terbaru bisa saja sudah diproses oleh user lain
            |--------------------------------------------------------------------------
            */

            $this->request->refresh();

            if ($this->request->status !== 'pending') {

                $this->dispatch(
                    'wirekit-toast',
                    variant: 'danger',
                    title: 'Tidak dapat diproses',
                    message: 'Pengajuan cuti ini sudah diproses sebelumnya.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Toast karena jatah tidak mencukupi
            |--------------------------------------------------------------------------
            */

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Jatah Cuti Tidak Mencukupi',
                message: "Pengajuan {$this->request->total_days} hari tidak dapat disetujui. " .
                    "Sisa jatah yang tersedia untuk tahun {$leaveYear} hanya {$availableDays} hari."
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
        | Refresh halaman Management Leave
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'leave-management-refresh'
        );


        /*
        |--------------------------------------------------------------------------
        | Refresh halaman employee jika masih terbuka
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'leave-request'
        );


        /*
        |--------------------------------------------------------------------------
        | Tutup modal approve
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-modal-close',
            name: 'approve-leave-' . $this->request->id
        );


        /*
        |--------------------------------------------------------------------------
        | Tutup modal detail management
        |--------------------------------------------------------------------------
        */

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
