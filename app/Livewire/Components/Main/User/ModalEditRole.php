<?php

namespace App\Livewire\Components\Main\User;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class ModalEditRole extends Component
{
    public User $user;
    public array $roleName = [];

    public function mount()
    {
        $this->roleName = $this->user->roles()->pluck("name")->toArray();
    }

    public function changeRole()
    {
        $this->authorize('assignUser', Role::class);
        $this->validate(['roleName' => ['required', 'exists:roles,name']]);

        $this->user->syncRoles($this->roleName);

        $this->dispatch('wirekit-modal-close', name: 'edit-role');
        $this->dispatch('change-user');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil merubah roles', message: "anda berhasil merubah role dari user {$this->user->name}");
    }


    public function canSubmit()
    {
        return filled($this->roleName);
    }

    public function render()
    {
        $roles = Role::all();

        return view('livewire.components.main.user.modal-edit-role', compact('roles'));
    }
}
