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
        // 1. Garantir que os planos profissionais existam com os limites corretos
        $plans = [
            ['name' => 'Plano Básico', 'max_patients' => 20],
            ['name' => 'Plano Profissional', 'max_patients' => 50],
            ['name' => 'Plano Premium', 'max_patients' => 0], // 0 como ilimitado
        ];

        foreach ($plans as $p) {
            DB::table('professional_plans')->updateOrInsert(
                ['name' => $p['name']],
                ['max_patients' => $p['max_patients'], 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // 2. Adicionar JSON de permissões na tabela de vínculo
        Schema::table('professional_patient', function (Blueprint $table) {
            $table->json('patient_permissions')->nullable()->after('patient_id');
            // Auditoria de quem vinculou (se for administrativo)
            $table->unsignedInteger('linked_by')->nullable()->after('patient_permissions');
            $table->string('linking_ip', 45)->nullable()->after('linked_by');
            $table->string('linking_device')->nullable()->after('linking_ip');

            $table->foreign('linked_by')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Adicionar campos de auditoria em professional_profiles
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->timestamp('last_audit_at')->nullable()->after('registration_expiry_date');
            $table->string('audit_status')->default('verified')->after('last_audit_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropColumn(['last_audit_at', 'audit_status']);
        });

        Schema::table('professional_patient', function (Blueprint $table) {
            $table->dropForeign(['linked_by']);
            $table->dropColumn(['patient_permissions', 'linked_by', 'linking_ip', 'linking_device']);
        });
    }
};
