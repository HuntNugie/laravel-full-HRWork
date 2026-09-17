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
    /*
    |--------------------------------------------------------------------------
    | DATA UTAMA
    |--------------------------------------------------------------------------
    */

    public Employees $employee;

    public Collection $entitlements;

    public Collection $leaveRequests;

    public int $leaveRequestVersion = 1;


    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    public string $segment = 'all';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;


    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->employee = Auth::user()->employees;

        $this->loadData();
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD DATA
    |--------------------------------------------------------------------------
    */

    private function loadData(): void
    {
        $contract = $this->employee->latestEmployeeContract;

        /*
        |--------------------------------------------------------------------------
        | Jatah Cuti
        |--------------------------------------------------------------------------
        */

        $this->entitlements = $contract
            ? $contract
            ->contractLeave()
            ->with('leaveType')
            ->get()
            : new Collection();


        /*
        |--------------------------------------------------------------------------
        | Riwayat Pengajuan Cuti
        |--------------------------------------------------------------------------
        */

        $query = $this->employee
            ->leaveRequest()
            ->with('leaveType')
            ->latest('created_at');


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($this->segment !== 'all') {
            $query->where('status', $this->segment);
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER DARI TANGGAL
        |--------------------------------------------------------------------------
        |
        | Menampilkan pengajuan yang periodenya beririsan
        | dengan tanggal yang dipilih.
        |
        */

        if ($this->dateFrom) {
            $query->whereDate(
                'end_date',
                '>=',
                $this->dateFrom
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER SAMPAI TANGGAL
        |--------------------------------------------------------------------------
        */

        if ($this->dateTo) {
            $query->whereDate(
                'start_date',
                '<=',
                $this->dateTo
            );
        }


        $this->leaveRequests = $query->get();
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER STATUS
    |--------------------------------------------------------------------------
    */

    public function updatedSegment(): void
    {
        $this->loadData();
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER DARI TANGGAL
    |--------------------------------------------------------------------------
    */

    public function updatedDateFrom(): void
    {
        $this->loadData();
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER SAMPAI TANGGAL
    |--------------------------------------------------------------------------
    */

    public function updatedDateTo(): void
    {
        $this->loadData();
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI RENTANG TANGGAL
    |--------------------------------------------------------------------------
    */

    public function updated(): void
    {
        if (
            $this->dateFrom &&
            $this->dateTo &&
            $this->dateFrom > $this->dateTo
        ) {
            $this->addError(
                'dateTo',
                'Sampai tanggal harus sama atau setelah dari tanggal.'
            );

            return;
        }

        $this->resetErrorBag('dateTo');
    }


    /*
    |--------------------------------------------------------------------------
    | USED LEAVE
    |--------------------------------------------------------------------------
    */

    public function usedLeave(int $leaveTypeId): int
    {
        return (int) $this->employee
            ->leaveRequest()
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->sum('total_days');
    }


    /*
    |--------------------------------------------------------------------------
    | REMAINING LEAVE
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
    | LEAVE PERCENTAGE
    |--------------------------------------------------------------------------
    */

    public function leavePercentage(
        ContractLeaveEntitlements $entitlement
    ): float {
        if ($entitlement->days <= 0) {
            return 0;
        }

        $used = $this->usedLeave(
            $entitlement->leave_type_id
        );

        return min(
            100,
            ($used / $entitlement->days) * 100
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REFRESH DARI CHILD COMPONENT
    |--------------------------------------------------------------------------
    */

    #[On('leave-request')]
    public function refresh(): void
    {
        $this->employee = Auth::user()->employees;

        $this->loadData();

        /*
        |--------------------------------------------------------------------------
        | Paksa child modal dibuat ulang
        |--------------------------------------------------------------------------
        */

        $this->leaveRequestVersion++;
    }


    /*
    |--------------------------------------------------------------------------
    | RESET FILTER
    |--------------------------------------------------------------------------
    */

    public function resetFilters(): void
    {
        $this->segment = 'all';
        $this->dateFrom = null;
        $this->dateTo = null;

        $this->resetValidation([
            'dateTo',
        ]);

        $this->loadData();
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.page.main.leave.leave-request'
        );
    }
}
