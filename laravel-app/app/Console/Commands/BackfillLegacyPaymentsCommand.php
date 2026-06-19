<?php

namespace App\Console\Commands;

use App\Services\LegacyPaymentBackfillService;
use Illuminate\Console\Command;

class BackfillLegacyPaymentsCommand extends Command
{
    protected $signature = 'finance:backfill-legacy-mp {--dry-run : Simular sem gravar}';

    protected $description = 'Importa mercadopago_payment_credits para payments + financial_logs (unificação legado).';

    public function handle(LegacyPaymentBackfillService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Modo dry-run — nenhum registo será gravado.');
        }

        if (! $dryRun && ! $this->confirm('Importar pagamentos legados MP para a tabela payments?')) {
            $this->info('Operação cancelada.');

            return self::SUCCESS;
        }

        $result = $service->run($dryRun);

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total legado', $result['total']],
                ['Criados em payments', $result['created']],
                ['Já existiam (ignorados)', $result['skipped']],
                ['Financial logs criados', $result['logs_created']],
            ]
        );

        if ($result['created'] > 0 && ! $dryRun) {
            app(\App\Services\SaaSMetricsService::class)->clearCache();
            app(\App\Services\ExecutiveDashboardService::class)->clearCache();
            $this->info('Cache de métricas invalidado.');
        }

        return self::SUCCESS;
    }
}
