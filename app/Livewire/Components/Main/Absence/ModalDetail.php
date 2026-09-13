<?php

namespace App\Livewire\Components\Main\Absence;

use App\Models\EmployeeAbsenceRequest;
use Livewire\Component;

class ModalDetail extends Component
{
    public EmployeeAbsenceRequest $absence;

    public function reject()
    {
        $this->absence->status = 'rejected';
        $this->absence->save();

        $this->dispatch('wirekit-modal-close', name: 'detail-absence');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil Menolak Pengajuan', message: "anda berhasil menolak pengajuan {$this->absence->employees->user->name}");
        $this->dispatch('update-absence');
    }
    public function approve()
    {
        $this->absence->status = 'approved';
        $this->absence->save();

        $this->dispatch('wirekit-modal-close', name: 'detail-absence');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil Menerima Pengajuan', message: "anda berhasil menerima pengajuan {$this->absence->employees->user->name}");

        $this->dispatch('update-absence');
    }
    public function render()
    {
        return view('livewire.components.main.absence.modal-detail');
    }
}
