<?php

namespace Database\Seeders;

use App\Models\divisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        divisi::create([
            'name' => "Development",
            'description' => "Divisi pengembangan aplikasi",
            'is_active' => 'inactive'
        ]);
    }
}
