<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use Livewire\Component;

class ModalCancelLeave extends Component
{
    public LeaveRequest $request;

    public function cancel()
    {
        $this->request->update([
            "status" => "cancelled"
        ]);

        $this->dispatch('wirekit-modal-close', name: 'cancel-leave');
        $this->dispatch('wirekit-modal-close', name: 'detail-leave');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil', message: 'berhasil membatalkan cuti');
        $this->dispatch('leave-request');
    }
    public function render()
    {
        return view('livewire.components.main.leave.modal-cancel-leave');
    }
}
