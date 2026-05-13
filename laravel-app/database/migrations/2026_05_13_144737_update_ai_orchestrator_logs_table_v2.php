<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_orchestrator_logs', function (Blueprint $table) {
            // Renomear campos existentes para alinhar com os novos requisitos
            $table->renameColumn('agent_type', 'agent_name');
            $table->renameColumn('tokens_used', 'total_tokens');
            $table->renameColumn('response_time_ms', 'execution_time_ms');

            // Adicionar novos campos de métricas e rastreabilidade
            $table->string('model_name')->after('agent_name')->nullable();
            $table->integer('input_tokens')->after('total_tokens')->default(0);
            $table->integer('output_tokens')->after('input_tokens')->default(0);
            $table->decimal('cost_usd', 10, 6)->after('output_tokens')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_orchestrator_logs', function (Blueprint $table) {
            $table->renameColumn('agent_name', 'agent_type');
            $table->renameColumn('total_tokens', 'tokens_used');
            $table->renameColumn('execution_time_ms', 'response_time_ms');

            $table->dropColumn(['model_name', 'input_tokens', 'output_tokens', 'cost_usd']);
        });
    }
};
