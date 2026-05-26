<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            ['name' => 'Visão Geral', 'code' => 'dashboard', 'category' => 'free'],
            ['name' => 'Meus Treinos', 'code' => 'my_workouts', 'category' => 'freemium'],
            ['name' => 'Alimentação', 'code' => 'nutrition', 'category' => 'freemium'],
            ['name' => 'Minha Evolução', 'code' => 'my_evolution', 'category' => 'premium'],
            ['name' => 'Comunidade NexShape', 'code' => 'community', 'category' => 'free'],
            ['name' => 'Exames e Medidas', 'code' => 'assessments', 'category' => 'freemium'],
            ['name' => 'Chat com IA', 'code' => 'ai_chat', 'category' => 'ai_credits'],
            ['name' => 'Interpretação de Exames', 'code' => 'ai_exam_interpretation', 'category' => 'ai_credits'],
            ['name' => 'Ajuste de Treino', 'code' => 'ai_workout_adjustment', 'category' => 'ai_credits'],
            ['name' => 'Ajuste de Dieta', 'code' => 'ai_diet_adjustment', 'category' => 'ai_credits'],
            ['name' => 'Orquestrador IA', 'code' => 'ai_orchestrator', 'category' => 'free'],
        ];

        foreach ($features as $feature) {
            \App\Models\AppFeature::updateOrCreate(
                ['code' => $feature['code']],
                [
                    'name' => $feature['name'],
                    'category' => $feature['category'],
                    'is_active' => true,
                    'show_lock' => in_array($feature['category'], ['premium', 'ai_credits']),
                    'show_badge' => $feature['category'] === 'premium',
                ]
            );
        }
    }
}
