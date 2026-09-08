<?php

namespace App\Livewire\Components\Main\Attendances;

use App\Models\EmployeeAbsenceRequest;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ModalIzin extends Component
{

    public string $type = "";

    public string $reason = "";

    public function submit()
    {
        $this->authorize('create', EmployeeAbsenceRequest::class);

        $this->validate([
            "type" => "required|in:sakit,izin",
            "reason" => "required|string"
        ]);

        if (Auth::user()?->employees?->employeeAbsenceRequest && Auth::user()->employees->employeeAbsenceRequest()->where('date', today())->exists()) {
            $this->dispatch('wirekit-toast', variant: 'danger', title: 'Ada masalah', message: 'anda sudah mengajukan izin untuk hari ini');
            return;
        }
        Auth::user()->employees->employeeAbsenceRequest()->create([
            'type' => $this->type,
            'date' => today(),
            'reason' => $this->reason
        ]);

        $this->dispatch('wirekit-modal-close', name: 'create-absence-request');
        $this->dispatch('update-data');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'mengajukan izin/sakit', message: "Berhasil mengajukan izin/sakit");
    }


    public function render()
    {
        return view('livewire.components.main.attendances.modal-izin');
    }
}
