<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use App\Service\LeaveRequestService;
use Livewire\Component;

class ModalRejectLeave extends Component
{
    public LeaveRequest $request;

    public ?string $rejectionReason = null;

    public function mount(LeaveRequest $request): void
    {
        $this->request = $request;
    }

    public function reject(LeaveRequestService $leaveRequestService): void
    {
        $this->validate([
            'rejectionReason' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ], [
            'rejectionReason.required' => 'Alasan penolakan wajib diisi.',
            'rejectionReason.min' => 'Alasan penolakan minimal 5 karakter.',
            'rejectionReason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        try {
            $this->request = $leaveRequestService->reject(
                request: $this->request,
                rejectedBy: auth()->id(),
                reason: $this->rejectionReason,
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

        $this->rejectionReason = null;
        $this->resetValidation();

        $this->dispatch('leave-management-refresh');

        $this->dispatch(
            'wirekit-modal-close',
            name: 'reject-leave-' . $this->request->id
        );

        $this->dispatch(
            'wirekit-modal-close',
            name: 'management-leave-detail-' . $this->request->id
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Pengajuan ditolak',
            message: 'Pengajuan cuti berhasil ditolak.'
        );
    }

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-reject-leave'
        );
    }
}
