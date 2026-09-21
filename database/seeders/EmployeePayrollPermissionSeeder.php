<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeePayrollPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect([
            'view-payroll-my',
            'show-payroll-my',
        ])->map(
            fn(string $name) => Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ])
        );

        $employeeRole = Role::firstOrCreate([
            'name' => 'Employee',
            'guard_name' => 'web',
        ]);

        $employeeRole->givePermissionTo($permissions);
    }
}
