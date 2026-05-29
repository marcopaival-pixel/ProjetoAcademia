<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expande isolamento multi-tenant (empresa) em logs IA e análises corporais.
     */
    public function up(): void
    {
        $tables = [
            'ai_orchestrator_logs',
            'body_analyses',
            'ai_chats',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'academy_company_id')) {
                    $after = Schema::hasColumn($tableName, 'clinic_id') ? 'clinic_id' : 'user_id';
                    $table->unsignedBigInteger('academy_company_id')->nullable()->after($after)->index();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['ai_orchestrator_logs', 'body_analyses', 'ai_chats'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'academy_company_id')) {
                    $table->dropColumn('academy_company_id');
                }
            });
        }
    }
};
