<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Índices recomendados pela auditoria de performance (Fases 1–3).
 * Idempotente: só cria se tabela, colunas e índice ainda não existirem.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->indexIfColumnsExist('payments', ['status', 'created_at'], 'idx_payments_status_created');
        $this->indexIfColumnsExist('financial_logs', ['transaction_id', 'action'], 'idx_finlog_tx_action');
        $this->indexIfColumnsExist('professional_appointments', ['professional_id', 'appointment_at'], 'idx_appt_prof_datetime');
        $this->indexIfColumnsExist('pacientes', ['profissional_id', 'status'], 'idx_pacientes_prof_status');
        $this->indexIfColumnsExist('professional_availabilities', ['professional_id', 'day_of_week'], 'idx_avail_prof_dow');
        $this->indexIfColumnsExist('appointment_waitlists', ['patient_id', 'status', 'requested_date'], 'idx_waitlist_patient_status_date');
        $this->indexIfColumnsExist('subscriptions', ['user_id', 'status'], 'idx_subscriptions_user_status');
        $this->indexIfColumnsExist('exercise_entries', ['academy_company_id', 'entry_date'], 'idx_exercise_company_date');
        $this->indexIfColumnsExist('water_entries', ['academy_company_id', 'entry_date'], 'idx_water_company_date');
        $this->indexIfColumnsExist('weight_entries', ['academy_company_id', 'weighed_at'], 'idx_weight_company_date');
        $this->indexIfColumnsExist('creditos_compras', ['status', 'created_at'], 'idx_creditos_compras_status_created');
        $this->indexIfColumnsExist('professional_finance_entries', ['professional_id', 'status', 'payment_date'], 'idx_prof_finance_prof_status_paydate');

        // Corrige alvo da migração 2026_05_29 (nome errado: mercado_pago_credits).
        $this->indexIfColumnsExist('mercadopago_payment_credits', ['academy_company_id', 'created_at'], 'mp_credits_company_created_idx');
    }

    public function down(): void
    {
        $indexes = [
            'payments' => 'idx_payments_status_created',
            'financial_logs' => 'idx_finlog_tx_action',
            'professional_appointments' => 'idx_appt_prof_datetime',
            'pacientes' => 'idx_pacientes_prof_status',
            'professional_availabilities' => 'idx_avail_prof_dow',
            'appointment_waitlists' => 'idx_waitlist_patient_status_date',
            'subscriptions' => 'idx_subscriptions_user_status',
            'exercise_entries' => 'idx_exercise_company_date',
            'water_entries' => 'idx_water_company_date',
            'weight_entries' => 'idx_weight_company_date',
            'creditos_compras' => 'idx_creditos_compras_status_created',
            'professional_finance_entries' => 'idx_prof_finance_prof_status_paydate',
            'mercadopago_payment_credits' => 'mp_credits_company_created_idx',
        ];

        foreach ($indexes as $table => $indexName) {
            $this->dropIndexIfExists($table, $indexName);
        }
    }

    private function indexIfColumnsExist(string $table, array $columns, string $indexName): void
    {
        if (! Schema::hasTable($table) || $this->indexExists($table, $indexName)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return;
            }
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->index($columns, $indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->selectOne(
            'SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$database, $table, $indexName]
        );

        return (int) ($result->cnt ?? 0) > 0;
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (! Schema::hasTable($table) || ! $this->indexExists($table, $indexName)) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                $blueprint->dropIndex($indexName);
            });
        } catch (\Throwable) {
            // índice pode ter sido removido manualmente
        }
    }
};
