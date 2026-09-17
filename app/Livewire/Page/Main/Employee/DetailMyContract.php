<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\EmployeeContract;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail Contract Saya'])]
class DetailMyContract extends Component
{
    public EmployeeContract $contract;

    public function mount(EmployeeContract $contract): void
    {
        $employee = Auth::user()->employees;

        abort_if(
            !$employee || $contract->employee_id !== $employee->id,
            404
        );

        $this->contract = $contract->load([
            'contractLeave.leaveType',
            'benefits' => function ($query) {
                $query->where('status', 'active');
            },
        ]);
    }

    public function render()
    {
        return view(
            'livewire.page.main.employee.detail-my-contract'
        );
    }
}
