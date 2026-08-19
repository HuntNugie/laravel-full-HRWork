<?php

namespace App\Livewire\Components\Main\Position;

use App\Models\Position;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormAdd extends Component
{

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

    public function saveJobdesk(array $jobdesk)
    {
        $this->jobdesk = $jobdesk;
    }
    public function store()
    {
        $this->authorize('create', Position::class);

        $position = Position::create([
            'name' => $this->name,
            'description' => $this->desc,
            'slug' => Str::slug($this->name),
            'min_salary_daily' => $this->salary,
            'is_active' => $this->isActive ? 'active' : 'inactive'
        ]);

        foreach ($this->jobdesk as $job) {
            $position->jobdesk()->create(
                [
                    'jobdesk' => $job
                ]
            );
        }

        $this->dispatch('wirekit-modal-close', name: 'create-position');
        $this->dispatch('create-position');

    }
    public function canSubmit()
    {
        return filled($this->name) && filled($this->desc) && filled($this->salary) && $this->getErrorBag()->isEmpty();
    }
    public function render()
    {
        return view('livewire.components.main.position.form-add');
    }
}
