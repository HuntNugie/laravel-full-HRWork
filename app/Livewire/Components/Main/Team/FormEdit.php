<?php

namespace App\Livewire\Components\Main\Team;

use App\Models\Divisi;
use App\Models\Employees;
use App\Models\Team;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormEdit extends Component
{
    public Team $team;

    #[Validate(['required', 'exists:employees,id'], message: [
        'supervisorId.required' => 'Supervisor wajib di isi',
        'supervisorId.exists' => 'tidak ada di pilihan',
    ])]
    public $supervisorId = '';


    #[Validate(['required', 'exists:divisis,id'], message: [
        'divisiId.required' => 'Divisi wajib di isi',
        'divisiId.exists' => 'Tidak ada data divisi'
    ])]
    public  $divisiId = '';

    #[Validate(['required', 'min:2', 'string'], message: [
        'name.required' => 'Nama Team wajib di isi',
        'name.min' => 'minimal nama 2 karakter',
        'name.string' => 'nama wajib berupa teks'
    ])]
    public string $name = '';

    #[Validate(['required'], message: [
        'desc.required' => 'deskripsi wajib di isi',
    ])]
    public string $desc = '';


    public bool $isActive = false;

    public function update()
    {
        $this->authorize('update', $this->team);
        $this->validate();

        $this->team->update([
            'name' => $this->name,
            'description' => $this->desc,
            'is_active' => $this->isActive ? 'active' : 'inactive',
            'supervisor_id' => $this->supervisorId,
            'divisi_id' => $this->divisiId
        ]);

        $this->dispatch('wirekit-modal-close', name: "update-team");
        $this->dispatch('update-team');
    }

    public function canSubmit()
    {
        return filled($this->divisiId) && filled($this->name) && filled($this->desc) && filled($this->supervisorId) && $this->getErrorBag()->isEmpty();
    }

    #[On('refresh-edit')]
    public function refresh(int $id)
    {
        $this->team = Team::findOrFail($id);
        $this->name = $this->team->name;
        $this->desc = $this->team->description;
        $this->isActive = $this->team->is_active === 'active' ? true : false;
        $this->supervisorId = $this->team->supervisor_id;
        $this->divisiId = $this->team->divisi->id;
    }
    public function render()
    {
        $supervisors = Employees::with('user')->whereHas('position', function ($q) {
            return $q->where('name', '=', 'supervisor');
        })->get()->pluck('user.name', 'id')->toArray();
        $divisis = Divisi::query()->pluck('name', 'id')->toArray();
        return view('livewire.components.main.team.form-edit', compact(['divisis', 'supervisors']));
    }
}
