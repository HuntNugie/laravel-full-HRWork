<?php

namespace App\Livewire\Page\Main\Leave;

use App\Models\ContractLeaveEntitlements;
use App\Models\Employees;
use App\Service\LeaveRequestService;
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
        $contract = app(LeaveRequestService::class)
            ->currentActiveContract($this->employee);


        /*
        |--------------------------------------------------------------------------
        | JATAH CUTI
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
        | RIWAYAT PENGAJUAN CUTI
        |--------------------------------------------------------------------------
        |
        | Semua histori tetap ditampilkan.
        |
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
            $query->where(
                'status',
                $this->segment
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER DARI TANGGAL
        |--------------------------------------------------------------------------
        |
        | Menampilkan pengajuan yang periodenya beririsan
        | dengan rentang tanggal yang dipilih.
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
    | CUTI SUDAH DIGUNAKAN
    |--------------------------------------------------------------------------
    |
    | Hanya approved pada tahun berjalan yang dihitung.
    |
    */

    public function usedLeave(int $leaveTypeId): int
    {
        $entitlement = $this->entitlements->firstWhere(
            'leave_type_id',
            $leaveTypeId
        );

        return $entitlement
            ? app(LeaveRequestService::class)->usedDays($entitlement, now()->year)
            : 0;
    }


    /*
    |--------------------------------------------------------------------------
    | CUTI PENDING
    |--------------------------------------------------------------------------
    |
    | Pending pada tahun berjalan dianggap sebagai jatah
    | yang sedang dipesan.
    |
    */

    public function pendingLeave(int $leaveTypeId): int
    {
        $entitlement = $this->entitlements->firstWhere(
            'leave_type_id',
            $leaveTypeId
        );

        return $entitlement
            ? app(LeaveRequestService::class)->pendingDays($entitlement, now()->year)
            : 0;
    }


    /*
    |--------------------------------------------------------------------------
    | SISA CUTI TERSEDIA
    |--------------------------------------------------------------------------
    |
    | Sisa tersedia =
    |
    | Jatah
    | - Approved tahun berjalan
    | - Pending tahun berjalan
    |
    */

    public function remainingLeave(
        ContractLeaveEntitlements $entitlement
    ): int {
        return app(LeaveRequestService::class)
            ->remainingDays($entitlement, now()->year);
    }


    /*
    |--------------------------------------------------------------------------
    | PERSENTASE CUTI
    |--------------------------------------------------------------------------
    |
    | Progress hanya menunjukkan cuti yang sudah benar-benar
    | digunakan / approved.
    |
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
    |
    | Dipanggil setelah:
    | - create leave
    | - cancel leave
    | - approve leave
    | - reject leave
    |
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
