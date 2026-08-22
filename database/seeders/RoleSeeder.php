<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $hr =  Role::create([
            'name' => 'HR',
            'guard_name' => 'web'
        ]);
        $admin =  Role::create([
            'name' => 'Administrator',
            'guard_name' => 'web'
        ]);
    }
}
