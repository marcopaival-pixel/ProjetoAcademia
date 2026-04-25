<?php

namespace Database\Seeders;

use App\Models\ExerciseCatalog;
use App\Models\Muscle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExerciseCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar para evitar duplicados
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ExerciseCatalog::truncate();
        DB::table('exercise_muscles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $catalog = [
            // PEITO
            [
                'name' => 'Supino Reto com Barra',
                'muscle_group' => 'Peito',
                'equipment' => 'Barra',
                'difficulty' => 'Intermediário',
                'instructions' => 'Exercício básico multiarticular para peitoral maior.',
                'muscles' => ['Peito', 'Tríceps', 'Deltoides']
            ],
            [
                'name' => 'Supino Inclinado com Halteres',
                'muscle_group' => 'Peito',
                'equipment' => 'Halteres',
                'difficulty' => 'Intermediário',
                'instructions' => 'Foco na porção superior do peitoral.',
                'muscles' => ['Peito', 'Tríceps', 'Deltoides']
            ],
            [
                'name' => 'Crucifixo Reto',
                'muscle_group' => 'Peito',
                'equipment' => 'Halteres',
                'difficulty' => 'Iniciante',
                'instructions' => 'Isolador para peitoral.',
                'muscles' => ['Peito']
            ],
            [
                'name' => 'Crossover Polia Alta',
                'muscle_group' => 'Peito',
                'equipment' => 'Polia',
                'difficulty' => 'Intermediário',
                'instructions' => 'Trabalho de isolamento com tensão constante.',
                'muscles' => ['Peito']
            ],

            // COSTAS
            [
                'name' => 'Levantamento Terra',
                'muscle_group' => 'Costas',
                'equipment' => 'Barra',
                'difficulty' => 'Avançado',
                'instructions' => 'Exercício de força total, foco em cadeia posterior.',
                'muscles' => ['Lombar', 'Isquiotibiais', 'Glúteos', 'Trapézio']
            ],
            [
                'name' => 'Puxada Pulley Frente',
                'muscle_group' => 'Costas',
                'equipment' => 'Máquina',
                'difficulty' => 'Iniciante',
                'instructions' => 'Desenvolvimento da largura das costas.',
                'muscles' => ['Latíssimo', 'Bíceps']
            ],
            [
                'name' => 'Remada Curvada com Barra',
                'muscle_group' => 'Costas',
                'equipment' => 'Barra',
                'difficulty' => 'Intermediário',
                'instructions' => 'Espessura das costas e estabilidade.',
                'muscles' => ['Latíssimo', 'Bíceps', 'Lombar']
            ],
            [
                'name' => 'Remada Unilateral (Serrote)',
                'muscle_group' => 'Costas',
                'equipment' => 'Halteres',
                'difficulty' => 'Iniciante',
                'instructions' => 'Remada com foco unilateral.',
                'muscles' => ['Latíssimo', 'Bíceps']
            ],

            // PERNAS
            [
                'name' => 'Agachamento Livre',
                'muscle_group' => 'Pernas',
                'equipment' => 'Barra',
                'difficulty' => 'Avançado',
                'instructions' => 'Rei dos exercícios de perna.',
                'muscles' => ['Quadríceps', 'Glúteos', 'Isquiotibiais', 'Lombar']
            ],
            [
                'name' => 'Leg Press 45º',
                'muscle_group' => 'Pernas',
                'equipment' => 'Máquina',
                'difficulty' => 'Iniciante',
                'instructions' => 'Foco em quadríceps com segurança lombar.',
                'muscles' => ['Quadríceps', 'Glúteos']
            ],
            [
                'name' => 'Cadeira Extensora',
                'muscle_group' => 'Pernas',
                'equipment' => 'Máquina',
                'difficulty' => 'Iniciante',
                'instructions' => 'Isolamento de quadríceps.',
                'muscles' => ['Quadríceps']
            ],
            [
                'name' => 'Mesa Flexora',
                'muscle_group' => 'Pernas',
                'equipment' => 'Máquina',
                'difficulty' => 'Iniciante',
                'instructions' => 'Isolamento de isquiotibiais.',
                'muscles' => ['Isquiotibiais']
            ],
            [
                'name' => 'Elevação de Panturrilha em Pé',
                'muscle_group' => 'Pernas',
                'equipment' => 'Máquina',
                'difficulty' => 'Iniciante',
                'instructions' => 'Trabalho de gastrocnêmios.',
                'muscles' => ['Panturrilha']
            ],

            // OMBROS
            [
                'name' => 'Desenvolvimento com Halteres',
                'muscle_group' => 'Ombros',
                'equipment' => 'Halteres',
                'difficulty' => 'Intermediário',
                'instructions' => 'Foco em deltoides anterior e lateral.',
                'muscles' => ['Deltoides', 'Tríceps']
            ],
            [
                'name' => 'Elevação Lateral',
                'muscle_group' => 'Ombros',
                'equipment' => 'Halteres',
                'difficulty' => 'Iniciante',
                'instructions' => 'Isolamento de deltoide lateral.',
                'muscles' => ['Deltoides']
            ],
            [
                'name' => 'Encolhimento de Ombros',
                'muscle_group' => 'Ombros',
                'equipment' => 'Halteres',
                'difficulty' => 'Iniciante',
                'instructions' => 'Foco em trapézio superior.',
                'muscles' => ['Trapézio']
            ],

            // BRAÇOS
            [
                'name' => 'Rosca Direta com Barra W',
                'muscle_group' => 'Braços',
                'equipment' => 'Barra',
                'difficulty' => 'Iniciante',
                'instructions' => 'Exercício clássico para bíceps.',
                'muscles' => ['Bíceps']
            ],
            [
                'name' => 'Rosca Martelo',
                'muscle_group' => 'Braços',
                'equipment' => 'Halteres',
                'difficulty' => 'Iniciante',
                'instructions' => 'Foco em braquial e antebraço.',
                'muscles' => ['Bíceps', 'Antebraço']
            ],
            [
                'name' => 'Tríceps Pulley (Corda)',
                'muscle_group' => 'Braços',
                'equipment' => 'Polia',
                'difficulty' => 'Iniciante',
                'instructions' => 'Isolamento de tríceps.',
                'muscles' => ['Tríceps']
            ],
            [
                'name' => 'Tríceps Testa',
                'muscle_group' => 'Braços',
                'equipment' => 'Barra',
                'difficulty' => 'Intermediário',
                'instructions' => 'Trabalho intenso da cabeça longa do tríceps.',
                'muscles' => ['Tríceps']
            ],
        ];

        foreach ($catalog as $item) {
            $muscles = $item['muscles'];
            unset($item['muscles']);
            
            $exercise = ExerciseCatalog::create(array_merge($item, [
                'is_active' => true
            ]));

            foreach ($muscles as $muscleName) {
                $muscle = Muscle::where('name', $muscleName)->first();
                if ($muscle) {
                    $exercise->muscles()->attach($muscle->id);
                }
            }
        }
    }
}
