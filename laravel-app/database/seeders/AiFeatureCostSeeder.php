<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AiFeatureCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $costs = [
            // Core AI Intelligence (Standard Codes)
            ['feature_code' => 'ai_chat', 'feature_name' => 'Chat com IA (NexBot)', 'credits_required' => 10],
            ['feature_code' => 'workout_adjustment', 'feature_name' => 'Ajuste de Treino Inteligente', 'credits_required' => 40],
            ['feature_code' => 'diet_adjustment', 'feature_name' => 'Ajuste de Dieta Inteligente', 'credits_required' => 40],
            ['feature_code' => 'body_analysis', 'feature_name' => 'Análise de Bioimpedância / Corporal', 'credits_required' => 50],
            ['feature_code' => 'assessment_comparison', 'feature_name' => 'Comparação Evolutiva IA', 'credits_required' => 60],
            ['feature_code' => 'exam_interpretation', 'feature_name' => 'Interpretação de Exames Laboratoriais', 'credits_required' => 100],
            ['feature_code' => 'advanced_report', 'feature_name' => 'Geração de Relatório de Performance', 'credits_required' => 80],

            // Specific Feature Mappings (Found in Controllers)
            ['feature_code' => 'chat_response', 'feature_name' => 'Chat com NexBot (Sessão)', 'credits_required' => 10],
            ['feature_code' => 'analyze_body_photo', 'feature_name' => 'Análise de Foto Corporal (Visão computacional)', 'credits_required' => 50],
            ['feature_code' => 'diet_audit', 'feature_name' => 'Auditoria Nutricional Semanal', 'credits_required' => 40],
            ['feature_code' => 'meal_suggestion', 'feature_name' => 'Sugestão de Refeição Inteligente', 'credits_required' => 20],
            ['feature_code' => 'supplement_suggestion', 'feature_name' => 'Sugestão de Suplementação / Stacks', 'credits_required' => 30],
            ['feature_code' => 'generate_workout', 'feature_name' => 'Prescrição de Treino via IA', 'credits_required' => 40],
            ['feature_code' => 'generate_diet', 'feature_name' => 'Prescrição de Dieta via IA', 'credits_required' => 40],
            ['feature_code' => 'generate_report', 'feature_name' => 'Relatório Clínico / Prescrição Médica IA', 'credits_required' => 60],
            
            // Administrative & Support
            ['feature_code' => 'support_ai', 'feature_name' => 'Suporte ao Cliente via IA', 'credits_required' => 5],
        ];

        foreach ($costs as $cost) {
            \App\Models\AiFeatureCost::updateOrCreate(
                ['feature_code' => $cost['feature_code']],
                [
                    'feature_name' => $cost['feature_name'],
                    'credits_required' => $cost['credits_required'],
                    'is_active' => true,
                ]
            );
        }
    }
}
