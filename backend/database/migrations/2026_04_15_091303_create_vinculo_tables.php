<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Planos de Profissionais
        Schema::create('professional_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('max_patients')->default(0); 
            $table->timestamps();
        });

        // 2. Adicionar campos ao usuário (Profissional)
        Schema::table('users', function (Blueprint $table) {
            $table->string('professional_code')->unique()->nullable()->after('status');
            $table->string('qr_code_path')->nullable()->after('professional_code');
            $table->unsignedBigInteger('professional_plan_id')->nullable()->after('qr_code_path');
            
            $table->foreign('professional_plan_id')->references('id')->on('professional_plans')->onDelete('set null');
        });

        // 3. Solicitações de Vínculo
        Schema::create('professional_patient_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('professional_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['patient_id', 'professional_id'], 'pp_request_index');
        });

        // 4. Vínculo Profissional-Paciente (Tabela de Relacionamento)
        Schema::create('professional_patient', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('professional_id');
            $table->unsignedInteger('patient_id');
            $table->timestamps();

            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['professional_id', 'patient_id']);
        });

        // 5. Atualizar Body Assessments com campos de vínculo
        Schema::table('body_assessments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('notes');
            $table->enum('created_by', ['patient', 'professional'])->default('professional')->after('status');
            $table->unsignedInteger('professional_id')->nullable()->after('user_id');
            
            // Novos campos conforme Vinculo.txt
            $table->string('blood_pressure', 20)->nullable()->after('calf_r');
            $table->unsignedSmallInteger('heart_rate')->nullable()->after('blood_pressure');
            
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('body_assessments', function (Blueprint $table) {
            $table->dropForeign(['professional_id']);
            $table->dropColumn(['status', 'created_by', 'professional_id', 'blood_pressure', 'heart_rate']);
        });

        Schema::dropIfExists('professional_patient');
        Schema::dropIfExists('professional_patient_requests');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['professional_plan_id']);
            $table->dropColumn(['professional_code', 'qr_code_path', 'professional_plan_id']);
        });

        Schema::dropIfExists('professional_plans');
    }
};
