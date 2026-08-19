<?php

namespace App\Livewire\Components\Main\Position;

use App\Models\Position;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormEdit extends Component
{
    public Position $position;


    #[Validate(['required', 'min:2', 'string'], message: [
        'name.required' => 'Nama wajib di isi',
        'name.min' => 'Minimal menggunakan 2 karakter',
        'name.string' => 'Nama harus bentuk teks',
    ])]
    public string $name = '';

    #[Validate(['required'], message: [
        'desc.required' => "deskripsi wajib di isi"
    ])]
    public string $desc = '';

    #[Validate(['required', 'numeric', 'min:0'], message: [
        'salary.required' => "gaji wajib di isi",
        'salary.number' => 'gaji harus berupa numeric',
        'salary.min' => 'gaji harus di isi minimal 0 '
    ])]
    public ?int $salary = null;
    public bool $isActive = false;

    public array $jobdesk = [];

    public function update()
    {
        $this->authorize('update', $this->position);

        $this->position->update([
            'name' => $this->name,
            'description' => $this->desc,
            'min_salary_daily' => $this->salary,
            'is_active' => $this->isActive ? 'active' : 'inactive'
        ]);

        $this->position->jobdesk()->delete();

        foreach ($this->jobdesk as $job) {
            $this->position->jobdesk()->create([
                'jobdesk' => $job
            ]);
        }

        $this->dispatch('wirekit-modal-close', name: 'edit-position');
        $this->dispatch('updated-position');
    }

    #[On('open-edit')]
    public function getData(int $id)
    {
        $this->position = Position::findOrFail($id);
        $this->name = $this->position->name;
        $this->desc = $this->position->description;
        $this->salary = $this->position->min_salary_daily;
        $this->isActive = $this->position->is_active == 'active' ? true : false;
        $this->jobdesk = $this->position->jobdesk->pluck('jobdesk')->toArray();
    }

    public function saveJobdesk(array $jobdesk)
    {
        $this->jobdesk = $jobdesk;
    }
    public function canSubmit()
    {
        return filled($this->name) && filled($this->desc) && filled($this->salary) && $this->getErrorBag()->isEmpty();
    }
    public function render()
    {
        return view('livewire.components.main.position.form-edit');
    }
}
