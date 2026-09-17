<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use Livewire\Component;

class ModalDetailManagementLeave extends Component
{
    public LeaveRequest $request;

    public function mount(LeaveRequest $request): void
    {
        $this->request = $request->load([
            'employees.user',
            'leaveType',
            'employeeContract',
        ]);
    }

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-detail-management-leave'
        );
    }
}
