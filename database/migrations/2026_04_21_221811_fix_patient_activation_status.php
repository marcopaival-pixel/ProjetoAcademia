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
        // Priorizar 'pacientes' que é a tabela definitiva do sistema multi-tenant
        $tableName = Schema::hasTable('pacientes') ? 'pacientes' : (Schema::hasTable('paciente_profissional') ? 'paciente_profissional' : null);
        
        if ($tableName) {
            // Verificar qual coluna de status/ativação existe
            $column = Schema::hasColumn($tableName, 'status') ? 'status' : (Schema::hasColumn($tableName, 'ativo') ? 'ativo' : null);
            
            if ($column) {
                try {
                    \Illuminate\Support\Facades\DB::table($tableName)->update([$column => 'Sim']);
                } catch (\Exception $e) {
                    // Logar erro mas permitir que a migração continue se for apenas falha de dados legados
                    \Illuminate\Support\Facades\Log::warning("Falha ao atualizar status na tabela $tableName: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible
    }
};
