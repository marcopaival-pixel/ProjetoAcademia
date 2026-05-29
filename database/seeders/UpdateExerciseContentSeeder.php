<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExerciseCatalog;

class UpdateExerciseContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercisesData = [
            'Agachamento Livre' => [
                'video_type' => 'youtube',
                'video_url' => 'https://www.youtube.com/watch?v=1Tq3Qd_n4pE',
                'tips' => [
                    'Mantenha os pés afastados na largura dos ombros.',
                    'Contraia o abdômen e mantenha o peito estufado.',
                    'Desça como se fosse sentar em uma cadeira, mantendo o peso nos calcanhares.',
                    'Os joelhos não devem ultrapassar a linha dos pés de forma excessiva.',
                    'Mantenha a curvatura natural da coluna durante todo o movimento.'
                ],
                'common_mistakes' => [
                    'Juntar os joelhos para dentro durante a subida (valgo dinâmico).',
                    'Curvar a coluna lombar no final do movimento ("butt wink").',
                    'Tirar os calcanhares do chão.',
                    'Inclinar o tronco excessivamente para a frente.'
                ]
            ],
            'Supino Reto' => [
                'video_type' => 'youtube',
                'video_url' => 'https://www.youtube.com/watch?v=sqOw2Y6uDWQ',
                'tips' => [
                    'Mantenha os pés fixos no chão, glúteos e escápulas apoiados no banco.',
                    'Contraia as escápulas (retração escapular) para estabilizar os ombros.',
                    'A barra deve descer até tocar levemente a linha dos mamilos ou o meio do peito.',
                    'Empurre a barra para cima expirando o ar.',
                    'Mantenha os cotovelos em um ângulo de aproximadamente 45 a 60 graus em relação ao tronco.'
                ],
                'common_mistakes' => [
                    'Abrir os cotovelos a 90 graus (sobrecarrega os ombros).',
                    'Bater a barra no peito para ganhar impulso.',
                    'Levantar o quadril do banco durante a força.',
                    'Descer a barra muito próximo ao pescoço.'
                ]
            ],
            'Puxada Alta' => [
                'video_type' => 'youtube',
                'video_url' => 'https://www.youtube.com/watch?v=CAwf7n6Luuc',
                'tips' => [
                    'Ajuste o apoio das pernas para ficar bem fixo no banco.',
                    'Incline levemente o tronco para trás e estufe o peito.',
                    'Inicie o movimento puxando com as escápulas e depois flexione os cotovelos.',
                    'Puxe a barra até a altura do queixo ou início do peito.',
                    'Controle a subida para alongar bem a musculatura das costas.'
                ],
                'common_mistakes' => [
                    'Balançar o tronco excessivamente para ganhar impulso.',
                    'Encolher os ombros em direção às orelhas (roubando com o trapézio).',
                    'Puxar a barra por trás da nuca (menor eficiência e maior risco de lesão).',
                    'Soltar o peso de forma descontrolada na subida.'
                ]
            ],
            'Leg Press' => [
                'video_type' => 'youtube',
                'video_url' => 'https://www.youtube.com/watch?v=IZxyjW7OSvc',
                'tips' => [
                    'Apoie totalmente as costas e o quadril no assento.',
                    'Posicione os pés na plataforma na largura dos ombros.',
                    'Destrave o aparelho e desça o peso controladamente até os joelhos chegarem perto de 90 graus.',
                    'Empurre o peso pelos calcanhares até quase estender as pernas, sem travar o joelho.',
                    'Mantenha os joelhos alinhados com as pontas dos pés durante o movimento.'
                ],
                'common_mistakes' => [
                    'Descer demais a ponto do quadril descolar do banco (risco lombar).',
                    'Travar (hiperextender) os joelhos no final da subida.',
                    'Aproximar os joelhos na hora de empurrar o peso.',
                    'Posicionar as mãos nos joelhos para ajudar a empurrar.'
                ]
            ],
            'Desenvolvimento' => [
                'video_type' => 'youtube',
                'video_url' => 'https://www.youtube.com/watch?v=qEwKCR5JCog',
                'tips' => [
                    'Mantenha a coluna reta e o abdômen contraído.',
                    'Inicie o movimento com os halteres na altura dos ombros, cotovelos levemente à frente.',
                    'Empurre os pesos para cima até quase encostarem um no outro.',
                    'Desça de forma controlada até a altura inicial.',
                    'Evite usar o impulso das pernas (se for desenvolvimento sentado).'
                ],
                'common_mistakes' => [
                    'Inclinar as costas muito para trás.',
                    'Bater os halteres no topo do movimento.',
                    'Descer os pesos muito rápido sem controle.',
                    'Abrir os cotovelos para trás exageradamente.'
                ]
            ]
        ];

        // Process default specific exercises
        foreach ($exercisesData as $name => $data) {
            $exercise = ExerciseCatalog::where('name', 'like', "%$name%")->first();
            
            if ($exercise) {
                $exercise->update([
                    'video_type' => $data['video_type'],
                    'video_url' => $data['video_url'],
                    'tips' => $data['tips'],
                    'common_mistakes' => $data['common_mistakes']
                ]);
            } else {
                // If the exercise doesn't exist, we can optionally create it. 
                // But generally a catalog should be pre-populated or managed by admin.
                // Assuming it exists or skipping if not.
            }
        }

        // Generic update for remaining exercises that have NO tips
        $remaining = ExerciseCatalog::whereNull('tips')->orWhere('tips', '[]')->get();
        foreach ($remaining as $ex) {
            $ex->update([
                'video_type' => 'none',
                'tips' => [
                    'Mantenha a postura correta durante toda a execução.',
                    'Controle a fase excêntrica (descida do peso).',
                    'Respire corretamente, expirando durante o esforço.'
                ],
                'common_mistakes' => [
                    'Usar muita carga prejudicando a técnica.',
                    'Fazer o movimento muito rápido e usando impulso.',
                    'Prender a respiração durante o exercício.'
                ]
            ]);
        }
    }
}
