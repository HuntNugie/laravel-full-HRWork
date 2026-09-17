<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Employees;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail Karyawan'])]
class DetailEmployee extends Component
{
    public Employees $employee;

    public Collection $leaveEntitlements;


    public function mount(Employees $employee): void
    {
        $this->employee = $employee->load([
            'user',
            'position',
            'team',
            'employeeContract',
            'employeeContract.contractLeave.leaveType',
            'profile',
        ]);

        $this->loadLeaveEntitlements();
    }


    #[On('change-team')]
    public function updateOnChangeTeam(): void
    {
        $this->employee->load([
            'user',
            'position',
            'team',
            'employeeContract',
            'employeeContract.contractLeave.leaveType',
            'profile',
        ]);

        $this->loadLeaveEntitlements();
    }


    #[On('change-photo')]
    public function updateOnChangeProfile(): void
    {
        $this->employee->load([
            'user',
            'position',
            'team',
            'employeeContract',
            'employeeContract.contractLeave.leaveType',
            'profile',
        ]);

        $this->loadLeaveEntitlements();
    }


    private function loadLeaveEntitlements(): void
    {
        $latestContract = $this->employee->latestEmployeeContract;

        $this->leaveEntitlements = $latestContract
            ? $latestContract->contractLeave
            : new Collection();
    }


    public function usedLeave(int $leaveTypeId): int
    {
        return (int) $this->employee
            ->leaveRequest()
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');
    }


    public function pendingLeave(int $leaveTypeId): int
    {
        return (int) $this->employee
            ->leaveRequest()
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'pending')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');
    }


    public function remainingLeave($entitlement): int
    {
        $used = $this->usedLeave($entitlement->leave_type_id);

        $pending = $this->pendingLeave($entitlement->leave_type_id);

        return max(
            0,
            (int) $entitlement->days - $used - $pending
        );
    }


    public function render()
    {
        return view(
            'livewire.page.main.employee.detail-employee'
        );
    }
}
