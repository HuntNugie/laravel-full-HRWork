<?php

namespace App\Livewire\Components\Main\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class ModalDelete extends Component
{
    public Role $role;

    public function deleteRole()
    {

        $this->authorize("delete", $this->role);
        $this->role->delete();

        $this->dispatch('wirekit-modal-close', name: 'delete-role');
        $this->dispatch('delete-role');
    }

    public function render()
    {
        return view('livewire.components.main.roles.modal-delete');
    }
}
