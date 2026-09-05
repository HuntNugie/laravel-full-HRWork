<?php

namespace App\Livewire\Components\Main\Roles;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AssignUser extends Component
{
    public string $search = '';

    public Role $role;

    public array $userSelect = [];

    public function submitAssign()
    {
        $this->authorize('assignUser', $this->role);
        foreach ($this->userSelect as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->assignRole($this->role);
            }
        }

        session()->flash('success', 'Berhasil menambahkan role ke user');
        $this->dispatch('wirekit-modal-close', name: 'assign-role');
        $this->dispatch('refreshPage');
    }
    public function render()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', $this->role->id);
        })->when($this->search, function ($query) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%");
        })->get();
        return view('livewire.components.main.roles.assign-user', compact('users'));
    }
}
