<?php

namespace App\Livewire\Page\Main\Roles;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;


#[Layout('layouts.main', ['title' => 'Halaman Create Role'])]
class CreateRole extends Component
{

    public array $permissionsValue = [];

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
        ]);
        dd($this->permissionsValue);

        // $role = \Spatie\Permission\Models\Role::create([
        //     'name' => ,
        // ]);

        // $role->syncPermissions($this->permissionsValue);

        // session()->flash('success', 'Role berhasil dibuat');
        // return redirect()->route('role.view');
    }
    public function render()
    {
        return view('livewire.page.main.roles.create-role');
    }
}
