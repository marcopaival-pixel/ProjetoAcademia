<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->indexIfColumnsExist('users', ['academy_company_id', 'created_at'], 'users_company_created_idx');
        $this->indexIfColumnsExist('subscriptions', ['academy_company_id', 'status'], 'subscriptions_company_status_idx');
        $this->indexIfColumnsExist('mercado_pago_credits', ['academy_company_id', 'created_at'], 'mp_credits_company_created_idx');
        $this->indexIfColumnsExist('food_entries', ['academy_company_id', 'entry_date'], 'food_entries_company_date_idx');
        $this->indexIfColumnsExist('financial_logs', ['academy_company_id', 'created_at'], 'financial_logs_company_created_idx');
        $this->indexIfColumnsExist('patients', ['clinic_id', 'created_at'], 'patients_clinic_created_idx');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('users', 'users_company_created_idx');
        $this->dropIndexIfExists('subscriptions', 'subscriptions_company_status_idx');
        $this->dropIndexIfExists('mercado_pago_credits', 'mp_credits_company_created_idx');
        $this->dropIndexIfExists('food_entries', 'food_entries_company_date_idx');
        $this->dropIndexIfExists('financial_logs', 'financial_logs_company_created_idx');
        $this->dropIndexIfExists('patients', 'patients_clinic_created_idx');
    }

    private function indexIfColumnsExist(string $table, array $columns, string $indexName): void
    {
        if (! Schema::hasTable($table)) {
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

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                $blueprint->dropIndex($indexName);
            });
        } catch (\Throwable) {
            // índice pode não existir em ambientes divergentes
        }
    }
};
