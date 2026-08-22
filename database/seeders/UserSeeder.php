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
            'status' => 'active'
        ]);

        $admin = User::create([
            'name' => 'Nugie kurniawan',
            'email' => 'nugiekurniawan02@gmail.com',
            'password' => bcrypt('nugitea123'),
            'status'=> 'active'
        ]);
        $admin->assignRole('Administrator');
        $hr->assignRole('HR');
    }
}
