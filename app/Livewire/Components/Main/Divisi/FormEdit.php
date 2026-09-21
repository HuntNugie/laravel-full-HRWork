<?php

namespace App\Livewire\Components\Main\Divisi;

use App\Models\Divisi;
use App\Models\Employees;
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

    #[Validate(['nullable', 'exists:employees,id'], message: [
        'managerId.exists' => 'Manager tidak ditemukan',
    ])]
    public $managerId = null;

    public bool $isActive = false;

    #[On('open-edit')]
    public function open(int $id)
    {
        $divisi = Divisi::findOrFail($id);

        $this->divisi = $divisi;
        $this->name = $divisi->name;
        $this->desc = $divisi->description;
        $this->managerId = $divisi->manager_id;
        $this->isActive = $divisi->is_active === 'active';
    }

    public function canSubmit()
    {
        return filled($this->name)
            && filled($this->desc)
            && $this->getErrorBag()->isEmpty();
    }

    public function update()
    {
        $this->authorize('update', $this->divisi);
        $this->validate();

        $this->divisi->update([
            'name' => $this->name,
            'description' => $this->desc,
            'is_active' => $this->isActive ? 'active' : 'inactive',
            'manager_id' => $this->managerId ?: null,
        ]);

        $this->reset([
            'name',
            'desc',
            'managerId',
            'isActive'
        ]);

        $this->dispatch('wirekit-modal-close', name: 'edit-division');
        $this->dispatch('update-divisi');
    }

    public function render()
    {
        $managers = Employees::with(['user', 'position'])
            ->where(function ($query) {
                $query
                    ->where(function ($query) {
                        $query->where('status_employee', 'active')
                            ->whereDoesntHave('managedDivisi')
                            ->whereHas('position', function ($query) {
                                $query->where('name', 'Manager');
                            });
                    })
                    ->when($this->managerId, function ($query) {
                        $query->orWhereKey($this->managerId);
                    });
            })
            ->get();

        if ($managers->isEmpty()) {
            $managers = Employees::with(['user', 'position'])
                ->where('status_employee', 'active')
                ->whereDoesntHave('managedDivisi')
                ->get();
        }

        $managers = $managers->mapWithKeys(function ($employee) {
            return [
                $employee->id => "{$employee->user->name} - " . ($employee->position->name ?? 'Tanpa posisi'),
            ];
        });

        return view('livewire.components.main.divisi.form-edit', [
            'managers' => $managers,
        ]);
    }
}
