<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Employees;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Contract Saya'])]
class MyContract extends Component
{
    public Employees $employee;

    public Collection $contracts;

    public ?object $activeContract = null;


    public function mount(): void
    {
        $this->employee = Auth::user()
            ->employees()
            ->with([
                'user',
                'employeeContract.contractLeave.leaveType',
                'employeeContract.benefits' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Semua contract
        |--------------------------------------------------------------------------
        */

        $this->contracts = $this->employee->employeeContract
            ->sortByDesc('start_date')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Contract aktif saat ini
        |--------------------------------------------------------------------------
        */

        $this->activeContract = $this->contracts
            ->first(
                fn($contract) =>
                $contract->status === 'active'
            );
    }


    public function render()
    {
        return view(
            'livewire.page.main.employee.my-contract'
        );
    }
}
