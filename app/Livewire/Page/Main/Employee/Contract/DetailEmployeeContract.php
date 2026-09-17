<?php

namespace App\Livewire\Page\Main\Employee\Contract;

use App\Models\EmployeeContract;
use App\Models\Employees;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman detail contract'])]
class DetailEmployeeContract extends Component
{
    public Employees $employee;

    public EmployeeContract $contract;

    public Collection $benefits;

    public Collection $leaveEntitlements;


    public function mount(Employees $employee, EmployeeContract $contract): void
    {
        $this->employee = $employee;

        $this->contract = $contract;


        /*
        |--------------------------------------------------------------------------
        | BENEFITS
        |--------------------------------------------------------------------------
        */

        $this->benefits = $this->contract
            ->benefits()
            ->where('status', 'active')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | JATAH CUTI CONTRACT
        |--------------------------------------------------------------------------
        |
        | Mengambil seluruh jenis cuti yang diberikan pada contract
        | beserta jumlah hari/jatahnya.
        |
        */

        $this->leaveEntitlements = $this->contract
            ->contractLeave()
            ->with('leaveType')
            ->get();
    }


    #[Computed]
    public function totalBenefits()
    {
        return $this->benefits->sum('pivot.amount');
    }


    public function render()
    {
        return view(
            'livewire.page.main.employee.contract.detail-employee-contract'
        );
    }
}
