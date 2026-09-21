<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GivePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $hr = Role::where('name', 'HR')
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
            'view-daily-status'
        ])
            ->where('guard_name', 'web')
            ->get();

        $hr->syncPermissions($permissions);

        $admin = Role::where('name', 'Administrator')
            ->where('guard_name', 'web')
            ->first();

        if ($admin) {
            $dailyStatusPermission = Permission::where('name', 'view-daily-status')
                ->where('guard_name', 'web')
                ->first();

            if ($dailyStatusPermission) {
                $admin->givePermissionTo($dailyStatusPermission);
            }
        }
    }
}
