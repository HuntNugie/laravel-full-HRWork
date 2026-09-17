<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Employees;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Data Saya'])]
class MyData extends Component
{
    public Employees $employee;

    public function mount(): void
    {
        $this->employee = Auth::user()
            ->employees()
            ->with([
                'user',
                'position',
                'team.divisi',
                'profile',
                'profile.addressProfile.village.district.regency.province',
                'profile.bankAccount.bank',
                'employeeContract',
            ])
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Load contract aktif / terbaru
        |--------------------------------------------------------------------------
        */

        $contract = $this->employee->latestEmployeeContract;

        if ($contract) {
            $contract->load([
                'contractLeave.leaveType',
                'benefits' => function ($query) {
                    $query->where('status', 'active');
                },
            ]);
        }
    }


    public function render()
    {
        return view(
            'livewire.page.main.employee.my-data'
        );
    }
}
