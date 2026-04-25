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
        Schema::create('admin_clinic_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_user_id');
            $table->foreign('admin_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('clinic_id')->constrained('academy_companies')->onDelete('cascade');
            $table->string('motivo_acesso');
            $table->text('descricao')->nullable();
            $table->timestamp('data_hora_entrada');
            $table->timestamp('data_hora_saida')->nullable();
            $table->string('ip')->nullable();
            $table->string('duracao_acesso')->nullable(); // Could be string like '15m 30s' or integer seconds. Using string as per example if needed, but integer is better for calculation. I'll use string as requested if it matches "120" or similar, but I'll stick to a descriptive string for now.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_clinic_access_logs');
    }
};
