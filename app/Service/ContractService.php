<?php

namespace App\Service;

use App\Models\ContractSequence;
use App\Models\EmployeeContract;
use Illuminate\Support\Facades\DB;

class ContractService
{

    public function previewContractNumber(): string
    {
        $year = now()->year;
        $month = now()->month;

        $lastNumber = ContractSequence::query()
            ->where('year', $year)
            ->where('month', $month)
            ->value('last_number') ?? 0;

        return $this->formatContractNumber(
            $year,
            $month,
            $lastNumber + 1
        );
    }




    private function formatContractNumber(
        int $year,
        int $month,
        int $number
    ): string {
        return 'CTR/' .
            $year . '/' .
            str_pad($month, 2, '0', STR_PAD_LEFT) . '/' .
            str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
