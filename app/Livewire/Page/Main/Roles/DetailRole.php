<?php

namespace App\Livewire\Page\Main\Roles;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.main', ['title' => 'Halaman Detail Role'])]
class DetailRole extends Component
{
    public Role $role;

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

    public function render()
    {
        return view('livewire.page.main.roles.detail-role');
    }
}
