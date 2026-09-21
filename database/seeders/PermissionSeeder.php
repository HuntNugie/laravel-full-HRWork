<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-divisi', 'create-divisi', 'update-divisi', 'delete-divisi', 'show-divisi',
            'view-position', 'create-position', 'view-employee', 'create-employee', 'view-team',
            'view-status-daily',
            'view-master-project', 'create-master-project', 'update-master-project',
            'view-division-project', 'create-division-project', 'update-division-project',
            'view-task', 'create-task', 'update-task', 'submit-task', 'review-task',
            'report-project-progress', 'submit-project-review', 'review-project',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
