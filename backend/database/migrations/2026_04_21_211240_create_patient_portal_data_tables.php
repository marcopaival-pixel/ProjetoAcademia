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
        // 1. Plano de Tratamento (Treatment Plan)
        Schema::create('patient_treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('professional_id');
            $table->text('diagnosis')->nullable();
            $table->text('objectives')->nullable();
            $table->text('care_plan')->nullable();
            $table->text('orientations')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Documentos do Paciente (Patient Documents)
        Schema::create('patient_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('professional_id');
            $table->string('title');
            $table->string('category'); // Receita, Laudo, Exame, Relatório
            $table->string('file_path');
            $table->string('file_type')->nullable(); // pdf, jpg, png
            $table->bigInteger('file_size')->nullable();
            
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Status do Acompanhamento (Tracking Status)
        // Adicionando campo à tabela de vínculo existente para tracking de status
        Schema::table('paciente_profissional', function (Blueprint $table) {
            $table->string('tracking_status')->nullable()->default('Início'); // Em andamento, Finalizado, etc.
            $table->text('professional_notes_for_patient')->nullable(); // Avisos do profissional
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paciente_profissional', function (Blueprint $table) {
            $table->dropColumn(['tracking_status', 'professional_notes_for_patient']);
        });
        Schema::dropIfExists('patient_documents');
        Schema::dropIfExists('patient_treatment_plans');
    }
};
