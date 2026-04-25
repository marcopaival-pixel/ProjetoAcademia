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
                'is_active' => true,
                'features' => ['ai_chat', 'ai_training', 'ai_nutrition', 'premium_dashboard', 'advanced_analytics']
            ],
            [
                'name' => 'Aluno Pro',
                'description' => 'A experiência definitiva em performance. IA sem limites, ferramentas de biohacking e suporte prioritário.',
                'type' => 'student',
                'price' => 49.90,
                'ai_credits' => 500,
                'max_workouts' => 9999,
                'max_diets' => 99,
                'max_assessments' => 99,
                'is_active' => true,
                'features' => ['ai_chat', 'ai_training', 'ai_nutrition', 'premium_dashboard', 'advanced_analytics', 'priority_support', 'biohacking_tools']
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
                'is_active' => $p['is_active']
            ]);
            $this->seedFeatures($plan, array_fill_keys($p['features'], true));
        }

        // --- PLANOS PARA PROFISSIONAIS ---
        $professionalPlans = [
            [
                'name' => 'Profissional Starter',
                'description' => 'Para profissionais independentes que estão iniciando sua jornada digital com gestão de até 5 alunos.',
                'type' => 'professional',
                'price' => 89.90,
                'ai_credits' => 250,
                'max_patients' => 5,
                'is_active' => true,
                'features' => ['patient_management', 'ai_prescription', 'assessment_tools']
            ],
            [
                'name' => 'Profissional Pro',
                'description' => 'O plano ideal para quem quer escalar sua consultoria com até 20 alunos, IA avançada e relatórios White Label.',
                'type' => 'professional',
                'price' => 149.90,
                'ai_credits' => 1000,
                'max_patients' => 20,
                'is_active' => true,
                'features' => ['patient_management', 'ai_prescription', 'assessment_tools', 'white_label_reports', 'financial_control']
            ],
            [
                'name' => 'Profissional Premium',
                'description' => 'Liberdade total para grandes consultorias. Até 50 alunos, CRM integrado e o máximo de automação inteligente.',
                'type' => 'professional',
                'price' => 249.90,
                'ai_credits' => 3000,
                'max_patients' => 50,
                'is_active' => true,
                'features' => ['patient_management', 'ai_prescription', 'assessment_tools', 'white_label_reports', 'financial_control', 'custom_branding', 'crm_integration']
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
                'is_active' => $p['is_active']
            ]);
            $this->seedFeatures($plan, array_fill_keys($p['features'], true));
        }

        // --- PLANOS PARA CLÍNICAS ---
        $clinicPlans = [
            [
                'name' => 'Clínica Basic',
                'description' => 'Infraestrutura completa para pequenas clínicas com até 3 profissionais integrados e faturamento centralizado.',
                'type' => 'clinic',
                'price' => 499.00,
                'ai_credits' => 5000,
                'max_professionals' => 3,
                'is_corporate' => true,
                'is_active' => true,
                'features' => ['multi_professional', 'clinic_dashboard', 'centralized_billing']
            ],
            [
                'name' => 'Clínica Pro',
                'description' => 'Gestão avançada para centros de saúde em crescimento com até 10 profissionais e acesso via API.',
                'type' => 'clinic',
                'price' => 899.00,
                'ai_credits' => 15000,
                'max_professionals' => 10,
                'is_corporate' => true,
                'is_active' => true,
                'features' => ['multi_professional', 'clinic_dashboard', 'centralized_billing', 'advanced_team_analytics', 'api_access']
            ],
            [
                'name' => 'Clínica Enterprise',
                'description' => 'A solução definitiva para grandes redes de academias ou hospitais sem limites de profissionais e SLA prioritário.',
                'type' => 'clinic',
                'price' => 1999.00,
                'ai_credits' => 50000,
                'max_professionals' => 99,
                'is_corporate' => true,
                'is_active' => true,
                'features' => ['multi_professional', 'clinic_dashboard', 'centralized_billing', 'advanced_team_analytics', 'api_access', 'custom_development', 'priority_sla']
            ],
        ];

        foreach ($clinicPlans as $p) {
            $plan = Plan::create([
                'name' => $p['name'], 
                'description' => $p['description'], 
                'type' => $p['type'], 
                'price' => $p['price'], 
                'ai_credits' => $p['ai_credits'],
                'max_professionals' => $p['max_professionals'] ?? 0,
                'is_corporate' => $p['is_corporate'] ?? false,
                'is_active' => $p['is_active']
            ]);
            $this->seedFeatures($plan, array_fill_keys($p['features'], true));
        }

        // --- AI CREDIT PACKAGES ---
        $packages = [
            ['name' => 'Starter', 'credits' => 100],
            ['name' => 'Professional', 'credits' => 500],
            ['name' => 'Clinic', 'credits' => 2000],
        ];

        foreach ($packages as $pkg) {
            $calc = $aiService->calculatePackagePrice($pkg['credits']);
            \App\Models\AiCreditPackage::create([
                'name' => $pkg['name'],
                'credits' => $pkg['credits'],
                'price' => $calc['price'],
                'is_active' => true,
            ]);
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
