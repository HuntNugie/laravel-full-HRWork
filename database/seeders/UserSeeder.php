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
        $superAdminEmail = env('SUPERADMIN_EMAIL', 'superadmin@gmail.com');
        $superAdminPassword = env('SUPERADMIN_PASSWORD');

        if (! $superAdminPassword) {
            throw new \RuntimeException(
                'SUPERADMIN_PASSWORD is not set. Add SUPERADMIN_PASSWORD to .env before running the database seeders.',
            );
        }

        $hr = User::updateOrCreate(
            ['email' => 'nugiekurniawan03@gmail.com'],
            [
                'name' => 'Muhammad nadin nugraha',
                'password' => bcrypt('nugitea123'),
                'status' => 'active',
            ],
        );

        $admin = User::updateOrCreate(
            ['email' => 'nugiekurniawan02@gmail.com'],
            [
                'name' => 'Nugie kurniawan',
                'password' => bcrypt('nugitea123'),
                'status' => 'active',
            ],
        );

        $superAdmin = User::updateOrCreate(
            ['email' => $superAdminEmail],
            [
                'name' => 'Super Admin',
                'password' => bcrypt($superAdminPassword),
                'status' => 'active',
            ],
        );

        $admin->syncRoles(['Administrator']);
        $hr->syncRoles(['HR']);
        $superAdmin->syncRoles(['super-admin']);
    }
}
