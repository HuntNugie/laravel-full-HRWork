<?php

namespace App\Livewire\Components\Main\Attendances;

use Livewire\Component;

class ModalDetailDailyStatus extends Component
{
    public array $row = [];

    public function render()
    {
        return view('livewire.components.main.attendances.modal-detail-daily-status');
    }
}
