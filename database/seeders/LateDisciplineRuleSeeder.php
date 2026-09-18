<?php

namespace Database\Seeders;

use App\Models\LateDisciplineRule;
use Illuminate\Database\Seeder;

class LateDisciplineRuleSeeder extends Seeder
{
    public function run(): void
    {
        LateDisciplineRule::query()->firstOrCreate(
            [],
            [
                'name' => 'Potongan Keterlambatan',
                'threshold' => 3,
                'period_type' => 'monthly',
                'action_type' => 'payroll_deduction',
                'action_amount' => 20000,
                'description' => 'Setiap kelipatan 3 kali keterlambatan dalam bulan yang sama dikenakan potongan gaji sebesar Rp20.000.',
            ]
        );
    }
}
