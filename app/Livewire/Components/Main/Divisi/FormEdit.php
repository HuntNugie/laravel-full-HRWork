<?php

namespace App\Livewire\Components\Main\Divisi;

use App\Models\Divisi;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormEdit extends Component
{

    public ?Divisi $divisi = null;

    #[Validate(['required', 'min:2', 'string'], message: [
        'name.required' => 'Nama Divisi wajib di isi',
        'name.min' => 'minimal nama 2 karakter',
        'name.string' => 'nama wajib berupa teks'
    ])]
    public string $name = '';

    #[Validate(['required'], message: [
        'desc.required' => 'deskripsi wajib di isi',
    ])]
    public string $desc = '';
    public bool $isActive = false;

    #[On('open-edit')]
    public function open(int $id)
    {
        $divisi = Divisi::findOrFail($id);
        $this->divisi = $divisi;
        $this->name = $divisi->name;
        $this->desc = $divisi->description;
        $this->isActive = $divisi->is_active === "active" ? true : false;
    }
    public function canSubmit()
    {
        return filled($this->name) && filled($this->desc) && $this->getErrorBag()->isEmpty();
    }

    public function update(){
        $this->authorize('update',$this->divisi);
        $status = $this->isActive ? "active" : "inactive";
        $this->divisi->update([
            'name' => $this->name,
            'description' => $this->desc,
            'is_active' => $status
        ]);

        $this->reset([
            'name',
            'desc',
            'isActive'
        ]);

        $this->dispatch('wirekit-modal-close',name:'edit-division');
        $this->dispatch('update-divisi');
    }
    public function render()
    {
        return view('livewire.components.main.divisi.form-edit');
    }
}
