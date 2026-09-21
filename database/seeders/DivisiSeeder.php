<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Divisi::updateOrCreate(
            ['name' => 'Development'],
            [
                'description' => 'Divisi pengembangan aplikasi',
                'is_active' => 'inactive',
            ]
        );
    }
}
