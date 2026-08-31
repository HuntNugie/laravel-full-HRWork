<?php

namespace App\Livewire\Components\Main\Benefit;

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Benefit;

class FormAdd extends Component
{
    #[Validate(['required', 'string', 'unique:benefits,name'], message: [
        'name.required' => 'Nama wajib di isi',
        'name.unique' => 'Nama tunjangan sudah ada'
    ])]
    public string $name = '';
    public bool $isActive = false;

    #[Validate(['required', 'string'], message: [
        'desc.required' => 'Deskripsi wajib di isi',
    ])]
    public string $desc = '';

    public function store()
    {
        $this->authorize('create', Benefit::class);
        $this->validate();
        Benefit::create([
            'name' => $this->name,
            'description' => $this->desc,
            'status' => $this->isActive ? 'active' : 'inactive'
        ]);

        $this->dispatch('wirekit-modal-close', name: 'create-benefit');
        $this->dispatch('create-benefit');
    }

    public function canSubmit()
    {
        return filled($this->name) && filled($this->desc) && $this->getErrorBag()->isEmpty();
    }
    public function render()
    {
        return view('livewire.components.main.benefit.form-add');
    }
}
