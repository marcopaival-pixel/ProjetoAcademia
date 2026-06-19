<?php

namespace App\Console\Commands;

use App\Services\CommissionService;
use Illuminate\Console\Command;

class ReleaseCommissionsCommand extends Command
{
    protected $signature = 'commission:release';

    protected $description = 'Libera comissões PENDENTES cuja carência (available_at) já expirou para DISPONIVEL.';

    public function handle(CommissionService $commissionService): int
    {
        $count = $commissionService->releaseAvailableCommissions();

        $this->info("Comissões liberadas: {$count}");

        return self::SUCCESS;
    }
}
