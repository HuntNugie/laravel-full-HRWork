<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use App\Service\LeaveRequestService;
use Livewire\Component;

class ModalApproveLeave extends Component
{
    public LeaveRequest $request;

    public function mount(LeaveRequest $request): void
    {
        $this->request = $request->load([
            'employees',
            'leaveType',
            'employeeContract',
        ]);
    }

    public function approve(LeaveRequestService $leaveRequestService): void
    {
        try {
            $this->request = $leaveRequestService->approve(
                request: $this->request,
                approvedBy: auth()->id(),
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

        $this->dispatch('leave-management-refresh');
        $this->dispatch('leave-request');

        $this->dispatch(
            'wirekit-modal-close',
            name: 'approve-leave-' . $this->request->id
        );

        $this->dispatch(
            'wirekit-modal-close',
            name: 'management-leave-detail-' . $this->request->id
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Pengajuan Disetujui',
            message: 'Pengajuan cuti berhasil disetujui.'
        );
    }

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-approve-leave'
        );
    }
}
