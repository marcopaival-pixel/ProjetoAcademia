<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 4 da auditoria: remove índices duplicados ou cobertos por compostos mais amplos.
 * Executar após validar com `php artisan app:db:index-explain`.
 */
return new class extends Migration
{
    /** @var array<string, list<string>> */
    private array $indexesToDrop = [
        'food_entries' => ['idx_user_food_date'],
        'water_entries' => ['idx_user_water_date'],
        'load_logs' => ['load_logs_user_id_exercise_id_index'],
    ];

    public function up(): void
    {
        foreach ($this->indexesToDrop as $table => $indexNames) {
            foreach ($indexNames as $indexName) {
                $this->dropIndexIfExists($table, $indexName);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('food_entries') && ! $this->indexExists('food_entries', 'idx_user_food_date')) {
            Schema::table('food_entries', function (Blueprint $table) {
                $table->index(['user_id', 'entry_date'], 'idx_user_food_date');
            });
        }

        if (Schema::hasTable('water_entries') && ! $this->indexExists('water_entries', 'idx_user_water_date')) {
            Schema::table('water_entries', function (Blueprint $table) {
                $table->index(['user_id', 'entry_date'], 'idx_user_water_date');
            });
        }

        if (Schema::hasTable('load_logs') && ! $this->indexExists('load_logs', 'load_logs_user_id_exercise_id_index')) {
            Schema::table('load_logs', function (Blueprint $table) {
                $table->index(['user_id', 'exercise_id'], 'load_logs_user_id_exercise_id_index');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

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
        if (! $this->indexExists($table, $indexName)) {
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
