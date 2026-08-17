<?php

namespace App\Livewire\Components\Main\Divisi;

use App\Models\Divisi;
use Livewire\Attributes\On;
use Livewire\Component;

class Delete extends Component
{   
    public ?Divisi $divisi;

    #[On('open-delete')]
    public function open(int $id){
        $this->divisi = Divisi::findOrFail($id);
    }
    public function delete(){
        $this->authorize('delete',$this->divisi);

        $this->divisi->delete();

        $this->dispatch('wirekit-modal-close',name:'delete-divisi');
        $this->redirectRoute('divisi.view',navigate:true);
    }
    public function render()
    {
        return view('livewire.components.main.divisi.delete');
    }
}
