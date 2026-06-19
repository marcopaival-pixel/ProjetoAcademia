<?php

namespace App\Console\Commands;

use App\Services\PaymentReconciliationService;
use Illuminate\Console\Command;

class ReconcilePaymentsCommand extends Command
{
    protected $signature = 'finance:reconcile {--days=30 : Período em dias} {--stale=7 : Dias para compras PENDENTE antigas}';

    protected $description = 'Concilia pagamentos, compras de crédito e financial_logs (auditoria financeira).';

    public function handle(PaymentReconciliationService $service): int
    {
        $days = max(1, (int) $this->option('days'));
        $stale = max(1, (int) $this->option('stale'));

        $report = $service->analyze($stale, now()->subDays($days));

        $this->info('Conciliação financeira — desde '.$report['period_since']);
        $this->table(
            ['Indicador', 'Valor'],
            [
                ['Compras crédito PENDENTE (> '.$stale.'d)', $report['stale_pending_credit_purchases']],
                ['Créditos PAGO sem payment_id', $report['paid_credits_without_payment_id']],
                ['Pagamentos sem financial_log', $report['paid_payments_without_financial_log']],
                ['Total payments (período)', 'R$ '.number_format($report['payments_total'], 2, ',', '.')],
                ['Total financial_logs (período)', 'R$ '.number_format($report['financial_logs_total'], 2, ',', '.')],
                ['Legado mercadopago_credits', 'R$ '.number_format($report['legacy_mercadopago_credits_total'], 2, ',', '.')],
                ['Divergência payments vs logs', 'R$ '.number_format($report['payments_vs_logs_divergence'], 2, ',', '.')],
            ]
        );

        if ($report['healthy']) {
            $this->info('Status: OK — sem divergências relevantes.');

            return self::SUCCESS;
        }

        $this->warn('Status: ATENÇÃO — revisar itens acima.');

        return self::FAILURE;
    }
}
