<?php

namespace App\Service;

use App\Models\WarningLetterSequence;
use Illuminate\Support\Facades\DB;

class WarningLetterService
{
    public function previewWarningLetterNumber(): string
    {
        $year = now()->year;
        $month = now()->month;

        $lastNumber = WarningLetterSequence::query()
            ->where('year', $year)
            ->where('month', $month)
            ->value('last_number') ?? 0;

        return $this->formatWarningLetterNumber(
            $year,
            $month,
            $lastNumber + 1
        );
    }

    public function generateWarningLetterNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;
            $month = now()->month;

            $sequence = WarningLetterSequence::query()
                ->where('year', $year)
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                WarningLetterSequence::create([
                    'year' => $year,
                    'month' => $month,
                    'last_number' => 1,
                ]);

                return $this->formatWarningLetterNumber(
                    $year,
                    $month,
                    1
                );
            }

            $sequence->increment('last_number');

            return $this->formatWarningLetterNumber(
                $year,
                $month,
                $sequence->last_number
            );
        });
    }

    private function formatWarningLetterNumber(
        int $year,
        int $month,
        int $number
    ): string {
        return 'SP/' .
            $year . '/' .
            str_pad($month, 2, '0', STR_PAD_LEFT) . '/' .
            str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
