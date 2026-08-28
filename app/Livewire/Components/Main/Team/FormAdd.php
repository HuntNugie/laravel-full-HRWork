<?php

namespace App\Livewire\Components\Main\Team;

use App\Models\Divisi;
use App\Models\Employees;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormAdd extends Component
{

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

    public function store()
    {
        $this->authorize('create', Team::class);
        $this->validate();
        $status = $this->isActive ? 'active' : 'inactive';
        Team::create([
            'name' => $this->name,
            'description' => $this->desc,
            'is_active' => $status,
            'divisi_id' => intval($this->divisiId),
            'supervisor_id' => $this->supervisorId,
        ]);

        $this->dispatch('wirekit-modal-close', name: 'create-team');
        $this->dispatch('create-team');
    }
    public function canSubmit()
    {
        return filled($this->divisiId) && filled($this->name) && filled($this->desc) && filled($this->supervisorId) && $this->getErrorBag()->isEmpty();
    }
    public function render()
    {
        $supervisors = Employees::with('user')->whereHas('position', function ($q) {
            return $q->where('name', '=', 'supervisor');
        })->get()->pluck('user.name', 'id')->toArray();
        $divisis = Divisi::query()->pluck('name', 'id')->toArray();
        return view('livewire.components.main.team.form-add', [
            'divisis' => $divisis,
            'supervisors' => $supervisors,
        ]);
    }
}
