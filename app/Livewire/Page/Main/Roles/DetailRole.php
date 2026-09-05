<?php

namespace App\Livewire\Page\Main\Roles;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.main', ['title' => 'Halaman Detail Role'])]
class DetailRole extends Component
{
    use WithPagination;

    public Role $role;

    public string $search = '';
    #[Computed]
    public function getPermissions()
    {
        return $this->role->permissions->groupBy(function ($permission) {
            return str($permission->name)
                ->afterLast('-')
                ->headline();
        });
    }

    public function mount(Role $role)
    {
        $this->authorize("view", $role);
        $this->role = $role->load(['permissions', 'users']);
    }

    #[On('refreshPage')]
    public function refreshPage()
    {
        $this->mount($this->role);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function removeUser($userId)
    {
        $user = $this->role->users()->find($userId);
        if ($user) {
            $user->removeRole($this->role);
            $this->dispatch(
                'wirekit-toast',
                variant: 'success',
                title: 'Saved',
                message: "Berhasil menghapus user dari role "
            );
        } else {
            session()->flash('error', 'User tidak ditemukan atau tidak memiliki role ini');
        }
    }

    public function render()
    {
        $users = $this->role->users()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->paginate(5);
        return view('livewire.page.main.roles.detail-role', compact('users'));
    }
}
