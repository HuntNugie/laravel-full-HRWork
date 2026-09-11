<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail role dan permission user'])]
class DetailRolePermission extends Component
{
    public User $user;

    public string $search = '';
    public function mount(User $user)
    {
        $this->user = $user->load(['roles']);
    }

    #[Computed]
    public function getPermissions($role)
    {
        return $role->permissions->groupBy(function ($permission) {
            return str($permission->name)
                ->afterLast('-')
                ->headline();
        });
    }


    public function render()
    {
        $roles = $this->user->roles()->when($this->search, function ($q) {
            return $q->where(function ($qd) {
                $qd->where('name', 'like', '%' . $this->search . '%')->orWhereHas('permissions', function ($qe) {
                    $qe->where('name', 'like', '%' . $this->search . '%');
                });
            });
        })->get();
        return view('livewire.page.main.user.detail-role-permission', compact('roles'));
    }
}
