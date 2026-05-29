<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $newRoles = [
            [
                'name'        => 'personal',
                'label'       => 'Personal Trainer',
                'description' => 'Profissional de educação física — gerencia alunos, treinos e avaliações individuais.',
            ],
            [
                'name'        => 'nutricionista',
                'label'       => 'Nutricionista',
                'description' => 'Profissional de nutrição — gerencia pacientes, dietas e acompanhamento nutricional.',
            ],
            [
                'name'        => 'academia',
                'label'       => 'Academia / Estúdio',
                'description' => 'Empresa B2B com múltiplos alunos e colaboradores vinculados.',
            ],
        ];

        foreach ($newRoles as $role) {
            $exists = DB::table('roles')->where('name', $role['name'])->exists();
            if (!$exists) {
                DB::table('roles')->insert(array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('name', ['personal', 'nutricionista', 'academia'])->delete();
    }
};
