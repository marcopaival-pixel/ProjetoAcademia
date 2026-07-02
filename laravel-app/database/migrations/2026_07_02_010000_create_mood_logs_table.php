<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('professional_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('set null');

            // Indicadores de bem-estar registrados pelo próprio paciente
            $table->tinyInteger('mood_score')->unsigned()->comment('Humor de 0 a 10');
            $table->tinyInteger('energy_level')->unsigned()->nullable()->comment('Energia de 0 a 10');
            $table->decimal('sleep_hours', 4, 1)->nullable()->comment('Horas de sono');
            $table->tinyInteger('stress_level')->unsigned()->nullable()->comment('Estresse de 0 a 10');
            $table->text('notes')->nullable()->comment('Notas livres do paciente');
            $table->boolean('is_confidential')->default(false)->comment('Verdadeiro se for nota do profissional — nunca expor ao paciente');

            $table->date('logged_at')->comment('Data do registro');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_logs');
    }
};
