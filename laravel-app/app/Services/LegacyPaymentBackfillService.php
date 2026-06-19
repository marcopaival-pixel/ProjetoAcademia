<?php

namespace App\Services;

use App\Models\FinancialLog;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyPaymentBackfillService
{
    /**
     * Importa registros de mercadopago_payment_credits para a tabela payments unificada.
     */
    public function run(bool $dryRun = false): array
    {
        if (! Schema::hasTable('mercadopago_payment_credits') || ! Schema::hasTable('payments')) {
            return ['total' => 0, 'created' => 0, 'skipped' => 0, 'logs_created' => 0];
        }

        $rows = DB::table('mercadopago_payment_credits')->orderBy('mp_payment_id')->get();
        $created = 0;
        $skipped = 0;
        $logsCreated = 0;

        foreach ($rows as $row) {
            $gatewayId = (string) $row->mp_payment_id;

            $exists = Payment::query()
                ->where('gateway', 'mercadopago')
                ->where('gateway_id', $gatewayId)
                ->exists();

            if ($exists) {
                $skipped++;

                continue;
            }

            if (! $dryRun) {
                $payment = Payment::create([
                    'user_id' => (int) $row->user_id,
                    'gateway' => 'mercadopago',
                    'gateway_id' => $gatewayId,
                    'amount' => (float) $row->transaction_amount,
                    'fee_amount' => 0,
                    'net_amount' => (float) $row->transaction_amount,
                    'currency' => $row->currency_id ?? 'BRL',
                    'status' => 'paid',
                    'payload' => [
                        'legacy_import' => true,
                        'plan_code' => $row->plan_code ?? null,
                        'source' => 'mercadopago_payment_credits',
                    ],
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->created_at ?? now(),
                ]);

                if (Schema::hasTable('financial_logs')) {
                    $hasLog = FinancialLog::query()
                        ->where('transaction_id', $gatewayId)
                        ->whereIn('action', ['PAYMENT_RECEIVED', 'AI_CREDITS_PURCHASED'])
                        ->exists();

                    if (! $hasLog) {
                        $action = ($row->plan_code ?? '') === 'ai_credits' || str_starts_with((string) ($row->plan_code ?? ''), 'ai_')
                            ? 'AI_CREDITS_PURCHASED'
                            : 'PAYMENT_RECEIVED';

                        FinancialLog::create([
                            'user_id' => (int) $row->user_id,
                            'action' => $action,
                            'amount' => (float) $row->transaction_amount,
                            'transaction_id' => $gatewayId,
                            'origin' => 'mercadopago_legacy_import',
                            'payload' => ['payment_id' => $payment->id, 'plan_code' => $row->plan_code ?? null],
                            'created_at' => $row->created_at ?? now(),
                            'updated_at' => $row->created_at ?? now(),
                        ]);
                        $logsCreated++;
                    }
                }
            }

            $created++;
        }

        return [
            'total' => $rows->count(),
            'created' => $created,
            'skipped' => $skipped,
            'logs_created' => $logsCreated,
            'dry_run' => $dryRun,
        ];
    }
}
