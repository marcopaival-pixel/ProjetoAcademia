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
        Schema::create('health_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Tipo de métrica: hrv, sleep_hours, sleep_quality, recovery_score, resting_hr, etc.
            $table->string('type', 50)->index(); 
            
            // Valor numérico da métrica
            $table->decimal('value', 12, 4);
            
            // Unidade de medida (ms, hours, %, bpm, etc.)
            $table->string('unit', 20)->nullable();
            
            // Fonte do dado: apple_health, garmin, whoop, manual, etc.
            $table->string('source', 50)->default('manual');
            
            // Data e hora do registro (importante para métricas que ocorrem em horários específicos, como sono)
            $table->timestamp('recorded_at')->index();
            
            // Dados adicionais específicos do wearable (ex: estágios do sono em JSON)
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Índice composto para buscas rápidas de histórico do usuário
            $table->index(['user_id', 'type', 'recorded_at'], 'idx_user_metric_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_metrics');
    }
};
