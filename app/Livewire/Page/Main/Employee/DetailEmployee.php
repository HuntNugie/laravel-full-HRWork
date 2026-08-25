<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail Karyawan'])]
class DetailEmployee extends Component
{
    public Employees $employee;
    public function mount(Employees $employee)
    {
        $this->employee = $employee->load(['user', 'position', 'team', 'employeeContract', 'profile']);
    }
    public function render()
    {
        return view('livewire.page.main.employee.detail-employee');
    }
}
