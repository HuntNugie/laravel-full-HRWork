<?php

namespace App\Livewire\Components\Main\Absence;

use App\Models\EmployeeAbsenceRequest;
use App\Service\AbsenceRequestService;
use Livewire\Component;

class ModalDetail extends Component
{
    public EmployeeAbsenceRequest $absence;

    public function reject(AbsenceRequestService $absenceRequestService): void
    {
        try {
            $this->absence = $absenceRequestService->reject(
                request: $this->absence,
                rejectedBy: auth()->id(),
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
            name: 'detail-absence'
        );
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil Menolak Pengajuan',
            message: "Pengajuan {$this->absence->employees->user->name} berhasil ditolak."
        );
        $this->dispatch('update-absence');
    }

    public function approve(AbsenceRequestService $absenceRequestService): void
    {
        try {
            $this->absence = $absenceRequestService->approve(
                request: $this->absence,
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

        $this->dispatch(
            'wirekit-modal-close',
            name: 'detail-absence'
        );
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil Menerima Pengajuan',
            message: "Pengajuan {$this->absence->employees->user->name} berhasil disetujui."
        );
        $this->dispatch('update-absence');
    }

    public function render()
    {
        return view('livewire.components.main.absence.modal-detail');
    }
}
