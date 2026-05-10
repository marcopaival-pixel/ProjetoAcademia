<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\PlanFeature::truncate();
        \App\Models\Plan::truncate();
        \App\Models\AiCreditPackage::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $aiService = app(\App\Services\AiCreditService::class);

        // --- PLANOS PARA ALUNOS ---
        $studentPlans = [
            [
                'name' => 'Free',
                'description' => 'Ideal para quem está começando e quer experimentar o poder da IA na sua rotina de treinos de forma gratuita.',
                'type' => 'student',
                'price' => 0,
                'ai_credits' => 20,
                'max_workouts' => 3,
                'max_diets' => 1,
                'max_assessments' => 1,
                'trial_days' => 0,
                'is_active' => true,
                'features' => ['ai_chat', 'workout_history', 'basic_analytics']
            ],
            [
                'name' => 'Aluno Premium',
                'description' => 'Para atletas dedicados que buscam evolução constante com acompanhamento inteligente ilimitado e planos nutricionais.',
                'type' => 'student',
                'price' => 29.90,
                'ai_credits' => 100,
                'max_workouts' => 10,
                'max_diets' => 5,
                'max_assessments' => 12,
                'trial_days' => 7,
                'is_active' => true,
                'features' => ['ai_chat', 'ai_training', 'ai_nutrition', 'premium_dashboard', 'advanced_analytics']
            ],
        ];

        foreach ($studentPlans as $p) {
            $plan = Plan::create([
                'name' => $p['name'], 
                'description' => $p['description'], 
                'type' => $p['type'], 
                'price' => $p['price'], 
                'ai_credits' => $p['ai_credits'],
                'max_workouts' => $p['max_workouts'] ?? 0,
                'max_diets' => $p['max_diets'] ?? 0,
                'max_assessments' => $p['max_assessments'] ?? 0,
                'trial_days' => $p['trial_days'] ?? 0,
                'is_active' => $p['is_active']
            ]);
            $this->seedFeatures($plan, array_fill_keys($p['features'], true));
        }

        // --- PLANOS PARA PROFISSIONAIS ---
        $professionalPlans = [
            [
                'name' => 'Profissional Pro',
                'description' => 'O plano ideal para quem quer escalar sua consultoria com até 20 alunos, IA avançada e relatórios White Label.',
                'type' => 'professional',
                'price' => 149.90,
                'ai_credits' => 1000,
                'max_patients' => 20,
                'trial_days' => 15,
                'is_active' => true,
                'features' => ['patient_management', 'ai_prescription', 'assessment_tools', 'white_label_reports', 'financial_control']
            ],
        ];

        foreach ($professionalPlans as $p) {
            $plan = Plan::create([
                'name' => $p['name'], 
                'description' => $p['description'], 
                'type' => $p['type'], 
                'price' => $p['price'], 
                'ai_credits' => $p['ai_credits'],
                'max_patients' => $p['max_patients'] ?? 0,
                'trial_days' => $p['trial_days'] ?? 0,
                'is_active' => $p['is_active']
            ]);
            $this->seedFeatures($plan, array_fill_keys($p['features'], true));
        }

        // --- PLANOS PARA CLÍNICAS (CORPORATIVOS) ---
        $clinicPlans = [
            [
                'name' => 'Clínica SaaS Enterprise',
                'description' => 'Modelo híbrido: Base fixa + valor por profissional adicional.',
                'type' => 'clinic',
                'price' => 499.00,
                'price_per_professional' => 49.00,
                'min_professionals' => 3,
                'ai_credits' => 15000,
                'max_professionals' => 99,
                'trial_days' => 30,
                'is_corporate' => true,
                'is_active' => true,
                'features' => ['multi_professional', 'clinic_dashboard', 'centralized_billing', 'advanced_team_analytics', 'api_access']
            ],
        ];

        foreach ($clinicPlans as $p) {
            $plan = Plan::create([
                'name' => $p['name'], 
                'description' => $p['description'], 
                'type' => $p['type'], 
                'price' => $p['price'], 
                'price_per_professional' => $p['price_per_professional'] ?? 0,
                'min_professionals' => $p['min_professionals'] ?? 1,
                'ai_credits' => $p['ai_credits'],
                'max_professionals' => $p['max_professionals'] ?? 0,
                'trial_days' => $p['trial_days'] ?? 0,
                'is_corporate' => $p['is_corporate'] ?? false,
                'is_active' => $p['is_active']
            ]);
            $this->seedFeatures($plan, array_fill_keys($p['features'], true));
        }
    }

    private function seedFeatures(Plan $plan, array $features): void
    {
        foreach ($features as $key => $enabled) {
            \App\Models\PlanFeature::create([
                'plan_id' => $plan->id,
                'feature_key' => $key,
                'is_enabled' => $enabled,
            ]);
        }
    }
}
