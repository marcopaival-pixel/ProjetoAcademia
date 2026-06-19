<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adicionar campos na tabela especialidades
        Schema::table('especialidades', function (Blueprint $table) {
            $table->string('client_term')->default('Paciente')->after('categoria')
                  ->comment('Termo usado para clientes desta especialidade: Paciente, Aluno, Cliente, etc.');
            $table->json('enabled_modules')->nullable()->after('client_term')
                  ->comment('Módulos habilitados: [\"treinos\", \"dietas\", \"prontuarios\"]');
        });

        // 2. Tabela pivot para Clínica x Especialidades
        Schema::create('clinic_especialidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->foreignId('especialidade_id')->constrained('especialidades')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['clinic_id', 'especialidade_id']);
        });

        // 3. Adicionar especialidade_id (Especialidade Principal) ao professional_profiles
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->foreignId('especialidade_id')->nullable()->after('profession_id')->constrained('especialidades')->onDelete('set null');
        });

        // 4. Tabela pivot para Profissional x Múltiplas Especialidades
        Schema::create('professional_profile_especialidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained('professional_profiles', 'id', 'prof_profile_fk')->onDelete('cascade');
            $table->foreignId('especialidade_id')->constrained('especialidades')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['professional_profile_id', 'especialidade_id'], 'prof_profile_especialidade_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_profile_especialidade');
        
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropForeign(['especialidade_id']);
            $table->dropColumn('especialidade_id');
        });

        Schema::dropIfExists('clinic_especialidade');

        Schema::table('especialidades', function (Blueprint $table) {
            $table->dropColumn(['client_term', 'enabled_modules']);
        });
    }
};
