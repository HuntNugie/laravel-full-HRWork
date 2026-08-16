<?php

namespace App\Livewire\Components\Main\Divisi;

use App\Models\Divisi;
use Livewire\Attributes\Validate;
use Livewire\Component;


class FormAdd extends Component
{
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


    public function store()
    {
        $this->authorize('create', Divisi::class);

        $status = $this->isActive ? "active" : "inactive";
        Divisi::create([
            'name' => $this->name,
            'description' => $this->desc,
            'is_active' => $status
        ]);

        $this->reset([
            'name',
            'desc',
            'isActive'
        ]);

        $this->dispatch('wirekit-modal-close',name:'create-division');
        $this->dispatch('create-divisi');
    }

    public function canSubmit()
    {
        return filled($this->name) && filled($this->desc) && $this->getErrorBag()->isEmpty();
    }
    public function render()
    {
        return view('livewire.components.main.divisi.form-add');
    }
}
