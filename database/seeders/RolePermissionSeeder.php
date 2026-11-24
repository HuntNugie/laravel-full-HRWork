<?php

namespace Database\Seeders;

use App\Support\HrAccess;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Role::findByName('administrator', 'web')->syncPermissions(HrAccess::ADMIN);
        // HR permissions are granted directly per user, so they are individually revocable.
        Role::findByName('hr', 'web')->syncPermissions([]);
    }
}
