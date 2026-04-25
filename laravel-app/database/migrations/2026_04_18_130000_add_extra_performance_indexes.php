<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adiciona índices complementares para otimização de performance em tabelas de alto volume
     * e consultas frequentes de relatórios e filtros de multi-tenancy/SaaS.
     */
    public function up(): void
    {
        // 1. Vínculos Profissional-Paciente (Performance de listagem por paciente e empresa)
        if (Schema::hasTable('paciente_profissional')) {
            Schema::table('paciente_profissional', function (Blueprint $table) {
                // Já existe index(['profissional_id', 'paciente_id'])
                $table->index('paciente_id', 'idx_paciente_pref');
                $table->index('empresa_id', 'idx_empresa_vinculo');
                $table->index('ativo', 'idx_ativo_vinculo');
            });
        }

        // 2. Registros de Nutrição e Atividade (Performance de relatórios globais por data)
        // Nota: O índice (user_id, entry_date) já existe, estes são para filtros sem user_id.
        Schema::table('food_entries', function (Blueprint $table) {
            $table->index('entry_date', 'idx_food_date');
        });

        Schema::table('water_entries', function (Blueprint $table) {
            $table->index('entry_date', 'idx_water_date');
        });

        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->index('entry_date', 'idx_exercise_date');
        });

        // 3. Pesos (Histórico por data)
        Schema::table('weight_entries', function (Blueprint $table) {
            $table->index('weighed_at', 'idx_weight_date');
        });

        // 4. Mercado Pago (Busca por status de assinaturas)
        Schema::table('mercadopago_subscriptions', function (Blueprint $table) {
            $table->index('status', 'idx_subscription_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('paciente_profissional')) {
            Schema::table('paciente_profissional', function (Blueprint $table) {
                $table->dropIndex('idx_paciente_pref');
                $table->dropIndex('idx_empresa_vinculo');
                $table->dropIndex('idx_ativo_vinculo');
            });
        }

        Schema::table('food_entries', function (Blueprint $table) {
            $table->dropIndex('idx_food_date');
        });

        Schema::table('water_entries', function (Blueprint $table) {
            $table->dropIndex('idx_water_date');
        });

        Schema::table('exercise_entries', function (Blueprint $table) {
            $table->dropIndex('idx_exercise_date');
        });

        Schema::table('weight_entries', function (Blueprint $table) {
            $table->dropIndex('idx_weight_date');
        });

        Schema::table('mercadopago_subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_subscription_status');
        });
    }
};
