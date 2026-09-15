<?php

namespace App\Livewire\Page\Main\Leave;

use App\Models\LeaveType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman detail jenis cuti'])]
class DetailLeaveType extends Component
{
    public LeaveType $leaveType;
    public function mount(LeaveType $leavetype)
    {
        $this->leaveType = $leavetype;
    }

    #[On('change-leave-type')]
    public function refreshPage()
    {
        $this->leaveType = $this->leaveType->fresh();
    }
    public function render()
    {
        return view('livewire.page.main.leave.detail-leave-type');
    }
}
