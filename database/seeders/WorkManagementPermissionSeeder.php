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
            'report-project-progress',
            'review-division-project',
            'view-task',
            'create-task',
            'assign-task',
            'update-task',
            'update-own-task',
            'submit-task',
            'review-task',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $rolePermissions = [
            'general-manager' => $permissions,
            'manager' => [
                'view-master-project',
                'view-division-project',
                'update-division-project',
                'assign-project-team',
                'report-project-progress',
                'review-division-project',
                'view-task',
                'assign-task',
                'update-task',
            ],
            'supervisor' => [
                'view-master-project',
                'view-division-project',
                'report-project-progress',
                'view-task',
                'create-task',
                'assign-task',
                'update-task',
                'review-task',
            ],
            'task-worker' => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'update-own-task',
                'submit-task',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissionNames);
        }
    }
}
