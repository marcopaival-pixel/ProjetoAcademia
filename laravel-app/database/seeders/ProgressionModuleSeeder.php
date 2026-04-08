<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgressionModuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Garantir Exercícios Base no catálogo
        $exercises = [
            ['name' => 'Supino Reto com Barra', 'muscle_group' => 'Peito', 'equipment' => 'Barra'],
            ['name' => 'Supino Inclinado com Halteres', 'muscle_group' => 'Peito', 'equipment' => 'Halteres'],
            ['name' => 'Desenvolvimento Militar', 'muscle_group' => 'Ombros', 'equipment' => 'Barra'],
            ['name' => 'Agachamento Livre', 'muscle_group' => 'Pernas', 'equipment' => 'Barra'],
            ['name' => 'Levantamento Terra', 'muscle_group' => 'Costas', 'equipment' => 'Barra'],
            ['name' => 'Remada Curvada', 'muscle_group' => 'Costas', 'equipment' => 'Barra'],
            ['name' => 'Puxada Pulley', 'muscle_group' => 'Costas', 'equipment' => 'Máquina'],
            ['name' => 'Rosca Direta', 'muscle_group' => 'Braços', 'equipment' => 'Barra'],
            ['name' => 'Tríceps Corda', 'muscle_group' => 'Braços', 'equipment' => 'Polia'],
        ];

        foreach ($exercises as $ex) {
            DB::table('exercises_catalog')->updateOrInsert(
                ['name' => $ex['name']],
                array_merge($ex, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 2. Se houver um usuário, criar um plano exemplo
        $user = DB::table('users')->first();
        if ($user) {
            $planId = DB::table('training_plans')->insertGetId([
                'user_id' => $user->id,
                'name' => 'Treino A (Foco Força)',
                'description' => 'Plano de exemplo focado em exercícios compostos.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $supino = DB::table('exercises_catalog')->where('name', 'Supino Reto com Barra')->first();
            if ($supino) {
                $tpExId = DB::table('training_plan_exercises')->insertGetId([
                    'training_plan_id' => $planId,
                    'exercise_id' => $supino->id,
                    'position' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 3 Séries de 5 reps
                for ($i = 1; $i <= 3; $i++) {
                    DB::table('exercise_sets')->insert([
                        'training_plan_exercise_id' => $tpExId,
                        'set_number' => $i,
                        'reps_target' => 5,
                        'weight_target' => 80,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Logs fictícios para o gráfico (histórico de 4 semanas)
                for ($w = 4; $w >= 0; $w--) {
                    $date = now()->subWeeks($w)->format('Y-m-d');
                    $baseWeight = 70 + ($w * 2.5); // Progressão linear de 2.5kg por semana
                    for ($s = 1; $s <= 3; $s++) {
                        DB::table('load_logs')->insert([
                            'user_id' => $user->id,
                            'training_plan_exercise_id' => $tpExId,
                            'exercise_id' => $supino->id,
                            'log_date' => $date,
                            'set_number' => $s,
                            'reps_done' => 5,
                            'weight_kg' => $baseWeight,
                            'rpe' => 8,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
