<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Obter os IDs das roles 'paciente' e 'aluno'
        $pacienteRole = DB::table('roles')->where('name', 'paciente')->first();
        $alunoRole = DB::table('roles')->where('name', 'aluno')->first();

        if ($pacienteRole && $alunoRole) {
            // Obter todos os mapeamentos de user_roles com o perfil de paciente
            $userRoles = DB::table('user_roles')->where('role_id', $pacienteRole->id)->get();

            foreach ($userRoles as $userRole) {
                // Verificar se o usuário já possui o perfil de aluno
                $hasAluno = DB::table('user_roles')
                    ->where('user_id', $userRole->user_id)
                    ->where('role_id', $alunoRole->id)
                    ->exists();

                if ($hasAluno) {
                    // Já possui, então removemos a associação duplicada
                    DB::table('user_roles')->where('id', $userRole->id)->delete();
                } else {
                    // Não possui, atualiza para aluno
                    DB::table('user_roles')->where('id', $userRole->id)->update([
                        'role_id' => $alunoRole->id,
                        'updated_at' => now(),
                    ]);
                }
            }

            // Remover permissões associadas à antiga role paciente
            DB::table('role_menu_permissions')->where('role_id', $pacienteRole->id)->delete();
            DB::table('role_permissions')->where('role_id', $pacienteRole->id)->delete();

            // Excluir a role 'paciente'
            DB::table('roles')->where('id', $pacienteRole->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // A reversão completa necessitaria de backups, não é necessária em ambiente controlado
    }
};
