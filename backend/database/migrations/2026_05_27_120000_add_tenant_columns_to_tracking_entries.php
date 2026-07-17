<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Colunas tenant diretas em registos de tracking (nutrição, treino, hidratação).
     */
    public function up(): void
    {
        $tables = [
            'food_entries',
            'exercise_entries',
            'water_entries',
            'weight_entries',
            'load_logs',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'clinic_id')) {
                    $table->unsignedBigInteger('clinic_id')->nullable()->after('user_id')->index();
                }
                if (! Schema::hasColumn($tableName, 'academy_company_id')) {
                    $table->unsignedBigInteger('academy_company_id')->nullable()->after('clinic_id')->index();
                }
            });
        }

        $this->backfillTenantColumnsFromUsers($tables);
    }

    public function down(): void
    {
        foreach (['food_entries', 'exercise_entries', 'water_entries', 'weight_entries', 'load_logs'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'academy_company_id')) {
                    $table->dropColumn('academy_company_id');
                }
                if (Schema::hasColumn($tableName, 'clinic_id')) {
                    $table->dropColumn('clinic_id');
                }
            });
        }
    }

    private function backfillTenantColumnsFromUsers(array $tables): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            DB::statement("
                UPDATE {$tableName} AS t
                INNER JOIN users AS u ON t.user_id = u.id
                SET t.academy_company_id = u.academy_company_id,
                    t.clinic_id = u.clinic_id
                WHERE t.academy_company_id IS NULL
            ");
        }
    }
};
