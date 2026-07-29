<?php

namespace App\Console\Commands;

use App\Models\Commission;
use Illuminate\Console\Command;

class ReleaseCommissionsCommand extends Command
{
    protected $signature = 'commissions:release-available {--dry-run : Apenas simula}';

    protected $description = 'Promove comissões PENDENTE para DISPONIVEL quando available_at venceu.';

    public function handle(): int
    {
        $query = Commission::query()
            ->where('status', Commission::STATUS_PENDENTE)
            ->whereNotNull('available_at')
            ->where('available_at', '<=', now());

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('Nenhuma comissão pendente para liberar.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("Simulação: {$count} comissão(ões) seriam liberadas.");

            return self::SUCCESS;
        }

        $updated = $query->update(['status' => Commission::STATUS_DISPONIVEL]);

        $this->info("Comissões liberadas: {$updated}");

        return self::SUCCESS;
    }
}
