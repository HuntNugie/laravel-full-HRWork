<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GivePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $hr = Role::where('name', 'hr')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $permissions = Permission::whereIn('name', [
            'view-divisi',
            'create-divisi',
            'update-divisi',
            'delete-divisi',
            'view-position',
            'create-position',
            'view-employee',
            'create-employee',
            'view-team',
            'show-divisi',
            'view-status-daily'
        ])
            ->where('guard_name', 'web')
            ->get();

        $hr->syncPermissions($permissions);

        $admin = Role::where('name', 'administrator')
            ->where('guard_name', 'web')
            ->first();

        if ($admin) {
            $dailyStatusPermission = Permission::where('name', 'view-status-daily')
                ->where('guard_name', 'web')
                ->first();

            if ($dailyStatusPermission) {
                $admin->givePermissionTo($dailyStatusPermission);
            }
        }
    }
}
