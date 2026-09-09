<?php

namespace App\Livewire\Page\Main\Employee\Contract;

use App\Models\EmployeeContract;
use App\Models\Employees;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman detail contract'])]
class DetailEmployeeContract extends Component
{
    public Employees $employee;
    public EmployeeContract $contract;
    public $benefits;

    public function mount(Employees $employee, EmployeeContract $contract)
    {
        $this->employee = $employee;
        $this->contract = $contract;
        $this->benefits = $this->contract->benefits;
    }

    #[Computed]
    public function totalBenefits()
    {
        return $this->benefits->sum('pivot.amount');
    }

    public function render()
    {
        return view('livewire.page.main.employee.contract.detail-employee-contract');
    }
}
