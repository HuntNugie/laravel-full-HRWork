<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class WorkManagementPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-master-project',
            'create-master-project',
            'update-master-project',
            'approve-master-project',
            'view-division-project',
            'create-division-project',
            'update-division-project',
            'assign-project-team',
            'review-division-project',
            'view-task',
            'create-task',
            'update-task',
            'update-own-task',

            // Legacy permissions are kept so existing databases remain compatible.
            'report-project-progress',
            'assign-task',
            'submit-task',
            'review-task',
            'submit-division-project-report',
            'review-division-project-report',
            'submit-division-project-to-gm',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $rolePermissions = [
            'general-manager' => [
                'view-master-project',
                'create-master-project',
                'update-master-project',
                'approve-master-project',
                'view-division-project',
                'create-division-project',
                'update-division-project',
                'assign-project-team',
                'review-division-project',
                'view-task',
                'update-task',
            ],

            'manager' => [
                'view-master-project',
                'view-division-project',
                'assign-project-team',
                'view-task',
                'update-task',
                'submit-division-project-to-gm',
            ],

            'supervisor' => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'create-task',
                'update-task',
            ],

            'task-worker' => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'update-own-task',
            ],
        ];

        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions(
            Permission::query()
                ->where('guard_name', 'web')
                ->get()
        );

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($permissionNames);
        }
    }
}
