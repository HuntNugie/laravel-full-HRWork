<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DailyStatusPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permission = Permission::firstOrCreate(
            [
                'name' => 'view-status-daily',
                'guard_name' => 'web',
            ]
        );

        $legacyPermission = Permission::query()
            ->where('name', 'view-daily-status')
            ->where('guard_name', 'web')
            ->first();

        if ($legacyPermission && $legacyPermission->id !== $permission->id) {
            foreach ($legacyPermission->roles as $role) {
                $role->givePermissionTo($permission);
            }

            $legacyPermission->delete();
        }

        foreach (['HR', 'Administrator'] as $roleName) {
            $role = Role::query()
                ->where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();

            if ($role) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
