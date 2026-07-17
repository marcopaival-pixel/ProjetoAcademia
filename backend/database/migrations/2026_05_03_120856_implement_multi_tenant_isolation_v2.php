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
        // 1. Garantir UUIDs para Identidades Públicas
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
        });

        Schema::table('academy_companies', function (Blueprint $table) {
            if (!Schema::hasColumn('academy_companies', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
        });

        // 2. Tabela de Vínculo: Clínica <-> Usuário (Multi-tenant M:N)
        // Permite que um paciente ou profissional pertença a várias clínicas
        Schema::create('clinic_user', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('user_id');
            $table->foreignId('academy_company_id')->constrained('academy_companies')->cascadeOnDelete();
            $table->string('role')->default('patient'); // admin, professional, patient, receptionist
            $table->string('status')->default('active'); // active, inactive, pending
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'academy_company_id', 'role']);
        });

        // 3. Tabela de Vínculo: Profissional <-> Paciente <-> Clínica
        // Isola o vínculo do profissional ao contexto de uma clínica específica
        Schema::create('professional_patient_clinic', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('professional_id');
            $table->unsignedInteger('patient_id');
            $table->foreignId('academy_company_id')->constrained('academy_companies')->cascadeOnDelete();
            $table->timestamps();

            $table->foreign('professional_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('patient_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['professional_id', 'patient_id', 'academy_company_id'], 'ppc_unique');
        });

        // 4. Colunas de Isolamento em Tabelas Clínicas (Garantir Global Scopes)
        Schema::table('patient_treatment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_treatment_plans', 'academy_company_id')) {
                $table->foreignId('academy_company_id')->nullable()->after('id')->constrained('academy_companies')->nullOnDelete();
            }
        });

        Schema::table('patient_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_documents', 'academy_company_id')) {
                $table->foreignId('academy_company_id')->nullable()->after('id')->constrained('academy_companies')->nullOnDelete();
            }
        });

        Schema::table('body_assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('body_assessments', 'academy_company_id')) {
                $table->foreignId('academy_company_id')->nullable()->after('id')->constrained('academy_companies')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('body_assessments', function (Blueprint $table) {
            $table->dropForeign(['academy_company_id']);
            $table->dropColumn('academy_company_id');
        });

        Schema::table('patient_documents', function (Blueprint $table) {
            $table->dropForeign(['academy_company_id']);
            $table->dropColumn('academy_company_id');
        });

        Schema::table('patient_treatment_plans', function (Blueprint $table) {
            $table->dropForeign(['academy_company_id']);
            $table->dropColumn('academy_company_id');
        });

        Schema::dropIfExists('professional_patient_clinic');
        Schema::dropIfExists('clinic_user');

        Schema::table('academy_companies', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
