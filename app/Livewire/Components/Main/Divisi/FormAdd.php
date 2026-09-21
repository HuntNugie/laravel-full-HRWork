<?php

namespace App\Livewire\Components\Main\Divisi;

use App\Models\Divisi;
use App\Models\Employees;
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

    #[Validate(['nullable', 'exists:employees,id'], message: [
        'managerId.exists' => 'Manager tidak ditemukan',
    ])]
    public $managerId = null;

    public bool $isActive = false;

    public function chooseManager($value): void
    {
        $this->managerId = filled($value) ? (int) $value : null;
    }

    public function store()
    {
        $this->authorize('create', Divisi::class);
        $this->validate();

        $status = $this->isActive ? 'active' : 'inactive';

        Divisi::create([
            'name' => $this->name,
            'description' => $this->desc,
            'is_active' => $status,
            'manager_id' => $this->managerId ?: null,
        ]);

        $this->reset([
            'name',
            'desc',
            'managerId',
            'isActive'
        ]);

        $this->dispatch('wirekit-modal-close', name: 'create-division');
        $this->dispatch('create-divisi');
    }

    public function canSubmit()
    {
        return filled($this->name)
            && filled($this->desc)
            && $this->getErrorBag()->isEmpty();
    }

    public function render()
    {
        $baseQuery = Employees::with(['user', 'position'])
            ->where('status_employee', 'active');

        $managers = (clone $baseQuery)
            ->whereHas('position', function ($query) {
                $query->where('name', 'Manager');
            })
            ->get();

        if ($managers->isEmpty()) {
            $managers = $baseQuery->get();
        }

        $managers = $managers->mapWithKeys(function ($employee) {
            return [
                $employee->id => "{$employee->user->name} - " . ($employee->position->name ?? 'Tanpa posisi'),
            ];
        });

        return view('livewire.components.main.divisi.form-add', [
            'managers' => $managers,
        ]);
    }
}
