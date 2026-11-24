<?php
namespace App\Console\Commands;
use App\Models\EmployeeContract;
use App\Service\ContractService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
#[Signature('app:expire-contracts')]
#[Description('Menandai kontrak yang melewati tanggal akhir sebagai expired')]
class ExpireContracts extends Command
{
    public function handle(ContractService $service): int
    {
        $count = 0;
        EmployeeContract::where('status', 'active')->whereNotNull('end_date')->whereDate('end_date', '<', today())
            ->chunkById(100, function ($contracts) use ($service, &$count) {
                foreach ($contracts as $contract) { if ($service->expire($contract)) { $count++; } }
            });
        $this->info("$count kontrak ditandai expired.");
        return self::SUCCESS;
    }
}
