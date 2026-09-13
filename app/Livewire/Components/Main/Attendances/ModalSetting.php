<?php

namespace App\Livewire\Components\Main\Attendances;

use App\Models\AttedanceSetting;
use Livewire\Component;

class ModalSetting extends Component
{
    public AttedanceSetting $attedanceSetting;
    public string $tolerance = "";
    public function mount()
    {
        $this->tolerance = $this->attedanceSetting->late_tolerance_minutes;
    }

    public function submit()
    {
        $this->validate([
            "tolerance" => ['required', 'numeric']
        ]);
        $this->attedanceSetting->late_tolerance_minutes = $this->tolerance;
        $this->attedanceSetting->save();

        $this->dispatch('wirekit-modal-close', name: 'edit-setting');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil merubah tolerasi menit', message: "anda berhasil merubah toleransi menit");
        $this->dispatch('change-tolerance');
    }
    public function render()
    {
        return view('livewire.components.main.attendances.modal-setting');
    }
}
