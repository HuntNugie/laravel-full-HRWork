<?php

namespace Database\Seeders;

use App\Models\AttedanceSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttedanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttedanceSetting::firstOrCreate([], [
            'late_tolerance_minutes' => 5,
        ]);
    }
}
