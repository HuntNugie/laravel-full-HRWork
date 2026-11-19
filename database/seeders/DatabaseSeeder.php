<?php

namespace Database\Seeders;

use Aliziodev\Wilayah\Database\Seeders\WilayahSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

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

            // Master alamat dari paket aliziodev/laravel-wilayah (4 level).
            // Data wilayah tidak otomatis terisi saat migration saja.
            WilayahSeeder::class,

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

        // Migrasi ulang tidak mengosongkan cache eksternal; hindari alamat usang.
        Artisan::call('wilayah:cache-clear');

        // Data transaksi dan akun contoh hanya aman untuk development/testing.
        // Jadi migrate:fresh --seed langsung menghasilkan HRIS yang sudah terisi.
        if (app()->environment(['local', 'testing'])) {
            $this->call(HRISDevelopmentSeeder::class);
        }
    }
}
