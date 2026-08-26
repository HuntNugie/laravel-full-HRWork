<?php

namespace App\Livewire\Components\Main\Benefit;

use App\Models\Benefit;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormEdit extends Component
{
    #[Validate(['required', 'string'], message: [
        'name.required' => 'Nama wajib di isi',
    ])]
    public string $name = '';
    public Benefit $benefit;

    #[Validate(['required', 'string'], message: [
        'desc.required' => 'Nama wajib di isi',
    ])]
    public string $desc = '';

    #[On('refresh-edit')]
    public function refresh(int $id)
    {
        $this->benefit = Benefit::findOrFail($id);
        $this->name = $this->benefit->name;
        $this->desc = $this->benefit->description;
    }




    public function update()
    {
        $this->authorize('update', $this->benefit);
        $this->validate(['name' => [
            Rule::unique('benefits', 'name')->ignore($this->benefit->id)
        ]]);
        $this->benefit->update([
            'name' => $this->name,
            'description' => $this->desc,
        ]);

        $this->dispatch('wirekit-modal-close', name: 'edit-benefit');
        $this->dispatch('update-benefit');
    }

    public function canSubmit()
    {
        return filled($this->name) && filled($this->desc) && $this->getErrorBag()->isEmpty();
    }

    public function render()
    {
        return view('livewire.components.main.benefit.form-edit');
    }
}
