<?php

namespace App\Console\Commands;

use App\Models\EmployeeContract;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:expire-contracts')]
#[Description('Menandai contract yang sudah melewati tanggal berakhir sebagai expired')]
class ExpireContracts extends Command
{
    public function handle(): int
    {
        $updated = EmployeeContract::query()
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', today())
            ->update([
                'status' => 'expired',
            ]);

        $this->info("{$updated} contract berhasil diubah menjadi expired.");

        return self::SUCCESS;
    }
}
