<?php

namespace App\Livewire\Page\Main\Leave;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman detail jenis cuti'])]
class DetailLeaveType extends Component
{
    public LeaveType $leaveType;

    public Collection $contractEntitlements;


    public function mount(LeaveType $leavetype): void
    {
        $this->loadLeaveType($leavetype);
    }


    private function loadLeaveType(LeaveType $leaveType): void
    {
        $this->leaveType = $leaveType->load([
            'contractLeaveEntitlement.employeeContract.employees.user',
        ]);

        $this->contractEntitlements =
            $this->leaveType->contractLeaveEntitlement
            ?? new Collection();
    }


    #[On('change-leave-type')]
    public function refreshPage(): void
    {
        $this->loadLeaveType(
            $this->leaveType->fresh()
        );
    }


    public function render()
    {
        return view(
            'livewire.page.main.leave.detail-leave-type'
        );
    }
}
