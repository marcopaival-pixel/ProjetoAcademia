<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Implementa otimizações de performance críticas:
     * 1. Coluna de cache para One Rep Max (1RM) em load_logs (evita cálculos matemáticos pesados no SQL).
     * 2. Índices compostos para consultas frequentes do Dashboard.
     */
    public function up(): void
    {
        // 1. Otimização da tabela load_logs
        Schema::table('load_logs', function (Blueprint $table) {
            // Adiciona coluna para armazenar o cálculo de 1RM (Epley Formula)
            // Fórmula: weight * (1 + reps / 30) -- ou a usada no sistema: weight / (1.0278 - 0.0278 * reps)
            if (!Schema::hasColumn('load_logs', 'one_rm')) {
                $table->decimal('one_rm', 10, 2)->nullable()->after('weight_kg');
                $table->index('one_rm', 'idx_load_one_rm');
            }
            
            // Índice composto para acelerar a busca de evolução por usuário/exercício/data
            $table->index(['user_id', 'log_date'], 'idx_user_log_date');
        });

        // Preencher a nova coluna com dados existentes
        DB::table('load_logs')->whereNull('one_rm')->update([
            'one_rm' => DB::raw('weight_kg / (1.0278 - 0.0278 * reps_done)')
        ]);

        // 2. Otimização de Food Entries (Busca comum por usuário + data)
        Schema::table('food_entries', function (Blueprint $table) {
            $table->index(['user_id', 'entry_date'], 'idx_user_food_date');
        });

        // 3. Otimização de Water Entries (Busca comum por usuário + data)
        Schema::table('water_entries', function (Blueprint $table) {
            $table->index(['user_id', 'entry_date'], 'idx_user_water_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('load_logs', function (Blueprint $table) {
            $table->dropIndex('idx_load_one_rm');
            $table->dropIndex('idx_user_log_date');
            $table->dropColumn('one_rm');
        });

        Schema::table('food_entries', function (Blueprint $table) {
            $table->dropIndex('idx_user_food_date');
        });

        Schema::table('water_entries', function (Blueprint $table) {
            $table->dropIndex('idx_user_water_date');
        });
    }
};
