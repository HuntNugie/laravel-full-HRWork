<?php

namespace App\Livewire\Page\Main\Roles;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('layouts.main', ['title' => 'Halaman Edit Role'])]
class EditRole extends Component
{
    public array $permissionsValue = [];


    public string $roleName = '';


    public function mount(Role $role)
    {
        $this->authorize("update", $role);
        $this->roleName = $role->name;
        $this->permissionsValue = $role->permissions()->pluck('name')->toArray();
    }

    #[Computed]
    public function getPermissions()
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return str($permission->name)
                    ->afterLast('-')
                    ->headline();
            });
    }


    public function selectAllPermissions()
    {
        if (count($this->permissionsValue) === Permission::count()) {
            $this->permissionsValue = [];
        } else {
            $this->permissionsValue = Permission::query()->pluck('name')->toArray();
        }
    }

    public function selectAllPermissionByGroup($group)
    {
        $permissionsInGroup = $this->getPermissions[$group]
            ->pluck('name')
            ->toArray();

        $allSelected = empty(array_diff(
            $permissionsInGroup,
            $this->permissionsValue
        ));

        if ($allSelected) {
            $this->permissionsValue = array_values(
                array_diff(
                    $this->permissionsValue,
                    $permissionsInGroup
                )
            );

            return;
        }

        $this->permissionsValue = array_values(
            array_unique([
                ...$this->permissionsValue,
                ...$permissionsInGroup,
            ])
        );
    }

    public function submit()
    {
        $this->validate([
            'permissionsValue' => ['required', 'array'],
            'permissionsValue.*' => ['exists:permissions,name'],
            'roleName' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        //   disini untuk update

        session()->flash('success', 'Role berhasil diupdate.');
        return $this->redirectRoute('role.view', navigate: true);
    }

    public function render()
    {
        return view('livewire.page.main.roles.edit-role');
    }
}
