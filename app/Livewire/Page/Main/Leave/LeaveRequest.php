<?php

namespace App\Livewire\Page\Main\Leave;

use App\Models\ContractLeaveEntitlements;
use App\Models\Employees;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Pengajuan Cuti Karyawan'])]
class LeaveRequest extends Component
{
    public Employees $employee;

    /**
     * Jatah cuti dari kontrak aktif.
     */
    public Collection $entitlements;

    /**
     * Riwayat pengajuan cuti karyawan.
     */
    public Collection $leaveRequests;

    public function mount(): void
    {
        $this->employee = Auth::user()->employees;

        $contract = $this->employee->latestEmployeeContract;

        /*
        |--------------------------------------------------------------------------
        | Jatah Cuti
        |--------------------------------------------------------------------------
        */
        $this->entitlements = $contract
            ? $contract->contractLeave()
            ->with('leaveType')
            ->get()
            : new Collection();

        /*
        |--------------------------------------------------------------------------
        | Riwayat Pengajuan Cuti
        |--------------------------------------------------------------------------
        */
        $this->leaveRequests = $this->employee
            ->leaveRequest()
            ->with('leaveType')
            ->latest()
            ->get();
    }

    /**
     * Mengambil jumlah cuti yang sudah digunakan.
     *
     * Hanya pengajuan dengan status approved
     * yang mengurangi jatah cuti.
     */
    public function usedLeave(int $leaveTypeId): int
    {
        return (int) $this->employee
            ->leaveRequest()
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->sum('total_days');
    }

    /**
     * Mengambil sisa cuti berdasarkan entitlement.
     */
    public function remainingLeave(ContractLeaveEntitlements $entitlement): int
    {
        $used = $this->usedLeave($entitlement->leave_type_id);

        return max(
            0,
            $entitlement->days - $used
        );
    }

    /**
     * Mengambil persentase cuti yang sudah digunakan.
     */
    public function leavePercentage(ContractLeaveEntitlements $entitlement): float
    {
        if ($entitlement->days <= 0) {
            return 0;
        }

        $used = $this->usedLeave($entitlement->leave_type_id);

        return min(
            100,
            ($used / $entitlement->days) * 100
        );
    }
    #[On('leave-request-created')]
    public function refresh()
    {
        $this->employee = Auth::user()->employees;

        $contract = $this->employee->latestEmployeeContract;

        /*
        |--------------------------------------------------------------------------
        | Jatah Cuti
        |--------------------------------------------------------------------------
        */
        $this->entitlements = $contract
            ? $contract->contractLeave()
            ->with('leaveType')
            ->get()
            : new Collection();

        /*
        |--------------------------------------------------------------------------
        | Riwayat Pengajuan Cuti
        |--------------------------------------------------------------------------
        */
        $this->leaveRequests = $this->employee
            ->leaveRequest()
            ->with('leaveType')
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.page.main.leave.leave-request');
    }
}
