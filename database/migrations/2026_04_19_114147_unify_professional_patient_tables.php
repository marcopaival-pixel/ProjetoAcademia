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
        $tableName = Schema::hasTable('paciente_profissional') ? 'paciente_profissional' : 'pacientes';
        
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'patient_permissions')) {
                    $table->json('patient_permissions')->nullable()->after(Schema::hasColumn($tableName, 'paciente_id') ? 'paciente_id' : 'user_id');
                }
                if (!Schema::hasColumn($tableName, 'linked_by')) {
                    $table->unsignedInteger('linked_by')->nullable()->after('patient_permissions');
                    $table->foreign('linked_by')->references('id')->on('users')->onDelete('set null');
                }
                if (!Schema::hasColumn($tableName, 'linking_ip')) {
                    $table->string('linking_ip', 45)->nullable()->after('linked_by');
                }
                if (!Schema::hasColumn($tableName, 'linking_device')) {
                    $table->string('linking_device')->nullable()->after('linking_ip');
                }
            });
        }

        // 2. Opcional: Migrar dados de professional_patient para paciente_profissional se existirem
        // Como o sistema está em desenvolvimento e a maioria das rotas usa a PT-BR, 
        // vamos apenas garantir que a PT-BR seja a definitiva.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paciente_profissional', function (Blueprint $table) {
            $table->dropForeign(['linked_by']);
            $table->dropColumn(['patient_permissions', 'linked_by', 'linking_ip', 'linking_device']);
        });
    }
};
