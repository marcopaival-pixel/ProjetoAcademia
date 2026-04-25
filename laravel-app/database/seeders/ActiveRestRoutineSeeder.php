<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActiveRestRoutineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routines = [
            [
                'id' => 1,
                'title' => 'Mobilidade de Quadril - Flow',
                'category' => 'Mobilidade',
                'duration' => '10 min',
                'intensity' => 'Leve',
                'recommended_level' => 'Iniciante',
                'thumbnail' => '/images/tutorials/hip_mobility.png',
                'guide_image' => '/images/Mobilidade.png',
                'exercises' => [
                    'Postura do Pombo (1 min/lado)',
                    '90/90 Hip Switches (10 reps)',
                    'Cossack Squat (10 reps)',
                    'Spiderman Lunge com Rotação (5 reps/lado)'
                ],
                'benefit' => 'Melhora a profundidade do agachamento e reduz dores lombares.',
                'execution_steps' => [
                    'Inicie com a Postura do Pombo para abrir o piriforme.',
                    'Transicione suavemente para os Hip Switches mantendo a coluna ereta.',
                    'Execute o Cossack Squat com controle, sem tirar o calcanhar do chão.',
                    'Finalize com Spiderman Lunge priorizando a rotação torácica.'
                ],
                'tips' => ['Mantenha a respiração nasal profunda.', 'Não force além do limite de dor.'],
                'common_errors' => ['Arquear as costas excessivamente.', 'Prender a respiração durante o esforço.'],
                'is_premium' => false,
                'order' => 1,
            ],
            [
                'id' => 2,
                'title' => 'Saúde do Ombro (Scapular Health)',
                'category' => 'Prevenção de lesão',
                'duration' => '8 min',
                'intensity' => 'Média',
                'recommended_level' => 'Intermediário',
                'thumbnail' => '/images/tutorials/shoulder_health.png',
                'guide_image' => '/images/saude_Ombro.png',
                'exercises' => [
                    'Face Pulls com Elástico (15 reps)',
                    'Alongamento de Peitoral no Batente (1 min)',
                    'Scapular Push-ups (15 reps)',
                    'YWT Raises (10 reps/cada)'
                ],
                'benefit' => 'Aumenta a estabilidade para supino e desenvolvimentos.',
                'execution_steps' => [
                    'Puxe o elástico em direção ao rosto, focando na retração das escápulas.',
                    'No alongamento de peitoral, gire o tronco para o lado oposto suavemente.',
                    'Nas flexões escapulares, mantenha os braços esticados o tempo todo.',
                    'YWT: mantenha o polegar para cima e sinta a musculatura das costas trabalhar.'
                ],
                'tips' => ['Conecte mente e músculo nas escápulas.', 'Use carga leve para focar na mecânica.'],
                'common_errors' => ['Dar trancos no elástico.', 'Subir os ombros em direção às orelhas.'],
                'is_premium' => false,
                'order' => 2,
            ],
            [
                'id' => 3,
                'title' => 'Alongamento profundo - Corpo Inteiro',
                'category' => 'Alongamento',
                'duration' => '15 min',
                'intensity' => 'Leve',
                'recommended_level' => 'Iniciante',
                'thumbnail' => '/images/tutorials/deep_stretch.png',
                'guide_image' => '/images/alogamento_profundo.png',
                'exercises' => [
                    'Child Pose (2 min)',
                    'Cat-Cow (15 reps)',
                    'Forward Fold (2 min)',
                    'Alongamento de Quadriceps em Pé (1 min/lado)'
                ],
                'benefit' => 'Acelera a recuperação e melhora o sono.',
                'execution_steps' => [
                    'Relaxe totalmente na Child Pose, alongando os braços à frente.',
                    'No Cat-Cow, sincronize o movimento com a respiração.',
                    'No Forward Fold, deixe o peso da cabeça tracionar a coluna.',
                    'No alongamento de quadríceps, mantenha os joelhos juntos.'
                ],
                'tips' => ['Ambiente calmo e luz baixa recomendados.', 'Foque em expirações longas.'],
                'common_errors' => ['Tentar alcançar o chão com as mãos à força.', 'Joelhos muito afastados no alongamento em pé.'],
                'is_premium' => false,
                'order' => 3,
            ],
            [
                'id' => 4,
                'title' => 'Desbloqueio de Cadeia Superior',
                'category' => 'Mobilidade',
                'duration' => '12 min',
                'intensity' => 'Intermediária',
                'recommended_level' => 'Avançado',
                'thumbnail' => '/images/tutorials/posterior_chain.png',
                'guide_image' => '/images/desbloqueio.png',
                'exercises' => [
                    'Spiderman Lunge com Rotação (10 reps)',
                    'Alongamento de Isquiotibiais Ativo (12 reps)',
                    'Cat-Cow (15 ciclos)',
                    'Pigeon Pose (90s / lado)'
                ],
                'benefit' => 'Melhora a postura e reduz riscos de lesão lombar.',
                'execution_steps' => [
                    'Lunge aberto, gire o tronco apontando a mão para o teto.',
                    'Eleve a perna estendida deitado, segure 2s no topo.',
                    'Arqueie e arredonde as costas lentamente.',
                    'Mantenha o quadril nivelado na postura do pombo.'
                ],
                'tips' => ['Ótimo protocolo pré ou pós treino de pernas.', 'Use a regra 4-4-8 de respiração.'],
                'common_errors' => ['Arquear a lombar excessivamente no Pigeon.', 'Prender a respiração.'],
                'is_premium' => true,
                'order' => 4,
            ]
        ];

        foreach ($routines as $routine) {
            \App\Models\ActiveRestRoutine::updateOrCreate(['id' => $routine['id']], $routine);
        }
    }
}
