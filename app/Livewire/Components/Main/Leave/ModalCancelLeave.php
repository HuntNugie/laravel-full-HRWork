<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use App\Service\LeaveRequestService;
use Livewire\Component;

class ModalCancelLeave extends Component
{
    public LeaveRequest $request;

    public function cancel(LeaveRequestService $leaveRequestService): void
    {
        try {
            $this->request = $leaveRequestService->cancel(
                request: $this->request
            );
        } catch (\LogicException $exception) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Tidak dapat diproses',
                message: $exception->getMessage()
            );

            return;
        }

        $this->dispatch(
            'wirekit-modal-close',
            name: 'cancel-leave'
        );
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Berhasil membatalkan cuti.'
        );
        $this->dispatch('leave-request');
    }

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-cancel-leave'
        );
    }
}
