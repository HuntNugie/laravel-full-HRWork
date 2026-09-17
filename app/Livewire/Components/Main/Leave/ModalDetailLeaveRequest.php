<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use Livewire\Component;

class ModalDetailLeaveRequest extends Component
{
    public LeaveRequest $request;

    public function mount(LeaveRequest $request): void
    {
        $this->request = $request->load([
            'leaveType',
            'employees.user',
        ]);
    }

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-detail-leave-request'
        );
    }
}
