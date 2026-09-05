<?php

namespace App\Livewire\Page\Main\Roles;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

#[Layout('layouts.main', ['title' => 'Halaman Create Role'])]
class CreateRole extends Component
{

    public array $permissionsValue = [];

    #[Validate([
        'required',
        'string',
        'max:255',
        'unique:roles,name'
    ], message: [
        'roleName.required' => 'Role name wajib di isi',
        'roleName.string' => 'Role name harus berupa string',
        'roleName.max' => 'Role name maksimal 255 karakter',
        'roleName.unique' => 'Role name sudah ada',
    ])]
    public string $roleName = '';


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

        $role = Role::create([
            'name' => Str::slug($this->roleName)
        ]);

        $role->syncPermissions($this->permissionsValue);

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Saved',
            message: 'Berhasil menambahkan role baru'
        );
        return $this->redirectRoute('role.view', navigate: true);
    }

    public function render()
    {
        return view('livewire.page.main.roles.create-role');
    }
}
