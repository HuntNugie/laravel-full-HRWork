<?php

namespace Database\Seeders;

use Aliziodev\Wilayah\Database\Seeders\WilayahSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([PermissionSeeder::class, RoleSeeder::class, RolePermissionSeeder::class, UserSeeder::class, WilayahSeeder::class, BankSeeder::class]);
        Artisan::call('wilayah:cache-clear');
    }
}
