<?php

namespace App\Console\Commands;

use App\Models\Commission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOrphanCommissionsCommand extends Command
{
    protected $signature = 'commissions:cleanup-orphans {--dry-run : Apenas simula}';

    protected $description = 'Cancela comissões sem pagamento válido ou com payment_id inexistente.';

    public function handle(): int
    {
        $orphans = Commission::query()
            ->where('status', '!=', Commission::STATUS_CANCELADO)
            ->where(function ($q) {
                $q->whereNull('payment_id')
                    ->orWhereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('payments')
                            ->whereColumn('payments.id', 'commissions.payment_id');
                    });
            })
            ->get();

        if ($orphans->isEmpty()) {
            $this->info('Nenhuma comissão órfã encontrada.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("Simulação: {$orphans->count()} comissão(ões) órfãs seriam canceladas.");

            return self::SUCCESS;
        }

        foreach ($orphans as $commission) {
            $commission->update([
                'status' => Commission::STATUS_CANCELADO,
                'notes' => trim(($commission->notes ?? '').' Cancelada: pagamento ausente (cleanup).'),
            ]);
        }

        $this->info("Comissões órfãs canceladas: {$orphans->count()}");

        return self::SUCCESS;
    }
}
