<?php

namespace App\Livewire\Components\Main\Holiday;

use App\Models\Holidays;
use Livewire\Component;

class ModalDelete extends Component
{
    public Holidays $holiday;

    public function delete()
    {
        $this->authorize("delete", $this->holiday);

        $this->holiday->delete();

        $this->dispatch('wirekit-modal-close', name: 'delete-holiday');
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Hapus jadwal libur',
            message: 'Jadwal berhasil di Hapus'
        );
        $this->dispatch('refreshPage');
    }
    public function render()
    {
        return view('livewire.components.main.holiday.modal-delete');
    }
}
