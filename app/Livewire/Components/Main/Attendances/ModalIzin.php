<?php

namespace App\Livewire\Components\Main\Attendances;

use App\Models\EmployeeAbsenceRequest;
use App\Service\AbsenceRequestService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ModalIzin extends Component
{
    public string $type = "";

    public string $reason = "";

    public function submit(AbsenceRequestService $absenceRequestService): void
    {
        $this->authorize('create', EmployeeAbsenceRequest::class);

        $this->validate([
            'type' => 'required|in:sakit,izin',
            'reason' => 'required|string|min:5|max:1000',
        ]);

        try {
            $absenceRequestService->create(
                employee: Auth::user()->employees,
                type: $this->type,
                reason: $this->reason,
            );
        } catch (\LogicException $exception) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Pengajuan Tidak Dapat Diproses',
                message: $exception->getMessage()
            );

            return;
        }

        $this->reset(['type', 'reason']);

        $this->dispatch(
            'wirekit-modal-close',
            name: 'create-absence-request'
        );
        $this->dispatch('update-data');
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil mengajukan izin/sakit',
            message: 'Berhasil mengajukan izin/sakit.'
        );
    }

    public function render()
    {
        return view('livewire.components.main.attendances.modal-izin');
    }
}
