<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core authorization and bootstrap accounts.
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,

            // HR master data.
            BankSeeder::class,

            // Final role -> permission mapping, including employee warning letter access.
            RolePermissionSeeder::class,

            // HR configuration required by the application.
            WorkTimeSeeder::class,
            AttedanceSettingSeeder::class,
            LateDisciplineRuleSeeder::class,
            UnpresentDisciplineRuleSeeder::class,

        ]);
    }
}
