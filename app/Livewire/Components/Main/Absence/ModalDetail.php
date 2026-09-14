<?php

namespace App\Livewire\Components\Main\Absence;

use App\Models\EmployeeAbsenceRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModalDetail extends Component
{
    public EmployeeAbsenceRequest $absence;

    public function reject()
    {
        $this->absence->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->id,
            'approved_at' => now()
        ]);

        $this->dispatch('wirekit-modal-close', name: 'detail-absence');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil Menolak Pengajuan', message: "anda berhasil menolak pengajuan {$this->absence->employees->user->name}");
        $this->dispatch('update-absence');
    }
    public function approve()
    {
        $this->absence->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->id,
            'approved_at' => now()
        ]);


        $this->dispatch('wirekit-modal-close', name: 'detail-absence');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil Menerima Pengajuan', message: "anda berhasil menerima pengajuan {$this->absence->employees->user->name}");

        $this->dispatch('update-absence');
    }
    public function render()
    {
        return view('livewire.components.main.absence.modal-detail');
    }
}
