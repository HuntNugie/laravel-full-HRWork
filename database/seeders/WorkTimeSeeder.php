<?php

namespace Database\Seeders;

use App\Models\WorkTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        foreach ($days as $day) {
            WorkTime::create([
                "day_of_week" => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_working_day' => true,
            ]);
        }
        WorkTime::create([
            "day_of_week" => "sabtu",
            'start_time' => '09:00:00',
            'end_time' => '14:00:00',
            'is_working_day' => true,
        ]);
        WorkTime::create([
            "day_of_week" => 'minggu',
            'start_time' => '00:00:00',
            'end_time' => '00:00:00',
            'is_working_day' => false,
        ]);
    }
}
