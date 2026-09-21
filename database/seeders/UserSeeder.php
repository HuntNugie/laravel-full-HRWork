<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hr = User::create([
            'name' => 'Muhammad nadin nugraha',
            'email' => 'nugiekurniawan03@gmail.com',
            'password' => bcrypt('nugitea123'),
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Nugie kurniawan',
            'email' => 'nugiekurniawan02@gmail.com',
            'password' => bcrypt('nugitea123'),
            'status' => 'active',
        ]);

        $superAdminEmail = env('SUPERADMIN_EMAIL', 'superadmin@gmail.com');
        $superAdminPassword = env('SUPERADMIN_PASSWORD');

        if (! $superAdminPassword) {
            throw new \RuntimeException(
                'SUPERADMIN_PASSWORD is not set. Add SUPERADMIN_PASSWORD to .env before running the database seeders.',
            );
        }

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => $superAdminEmail,
            'password' => bcrypt($superAdminPassword),
            'status' => 'active',
        ]);

        $admin->assignRole('Administrator');
        $hr->assignRole('HR');
        $superAdmin->assignRole('super-admin');
    }
}
