<?php

namespace Database\Seeders;

use App\Models\UnpresentDisciplineRule;
use Illuminate\Database\Seeder;

class UnpresentDisciplineRuleSeeder extends Seeder
{
    public function run(): void
    {
        UnpresentDisciplineRule::query()->firstOrCreate(
            [],
            [
                'threshold' => 3,
                'period_type' => 'monthly',
                'description' => 'Ketidakhadiran tanpa keterangan sebanyak 3 kali dalam bulan yang sama dapat dikenakan Surat Peringatan.',
            ]
        );
    }
}
