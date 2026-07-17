<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\PlanFeature::truncate();
        DB::table('plan_roles')->truncate();
        Plan::truncate();
        \App\Models\AiCreditPackage::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- PLANOS PARA ALUNOS (B2C) ---
        $studentPlans = [
            [
                'name'            => 'Free',
                'description'     => 'Acesso básico gratuito para acompanhar seus treinos e evolução inicial.',
                'type'            => 'student',
                'price'           => 0,
                'ai_credits'      => 10,
                'max_workouts'    => 3,
                'max_diets'       => 1,
                'max_assessments' => 1,
                'trial_days'      => 0,
                'is_active'       => true,
                'roles'           => ['aluno'],
                'features'        => [
                    'workout_view', 
                    'diet_view', 
                    'basic_chat', 
                    'community_access', 
                    'agenda_view',
                    'dashboard_basic',
                    'profile_access'
                ],
            ],
            [
                'name'            => 'Premium',
                'description'     => 'Experiência completa com IA, bioimpedância detalhada e relatórios de performance.',
                'type'            => 'student',
                'price'           => 29.90,
                'ai_credits'      => 1000,
                'max_workouts'    => 20,
                'max_diets'       => 10,
                'max_assessments' => 50,
                'trial_days'      => 7,
                'is_active'       => true,
                'roles'           => ['aluno'],
                'features'        => [
                    'workout_view', 
                    'diet_view', 
                    'full_chat', 
                    'ai_insights', 
                    'bioimpedance_history', 
                    'evolution_photos', 
                    'ai_training', 
                    'ai_nutrition',
                    'medical_records',
                    'advanced_reports',
                    'workout_log',
                    'active_rest',
                    'trophies_access'
                ],
            ],
        ];

        // --- PLANOS PARA PROFISSIONAIS (Personal/Nutri) ---
        $proPlans = [
            [
                'name'         => 'Profissional Starter',
                'description'  => 'Para profissionais que estão começando sua consultoria digital.',
                'type'         => 'professional',
                'price'        => 29.90,
                'ai_credits'   => 2000,
                'max_patients' => 10,
                'trial_days'   => 14,
                'is_active'    => true,
                'roles'        => ['personal', 'nutricionista'],
                'features'     => ['patient_management', 'create_workout', 'training_builder', 'nutrition_builder', 'agenda_basics'],
            ],
            [
                'name'         => 'Profissional PRO',
                'description'  => 'O plano ideal para quem quer escalar sua base de clientes com IA e relatórios.',
                'type'         => 'professional',
                'price'        => 59.90,
                'ai_credits'   => 5000,
                'max_patients' => 50,
                'trial_days'   => 14,
                'is_active'    => true,
                'roles'        => ['personal', 'nutricionista'],
                'features'     => ['patient_management', 'create_workout', 'training_builder', 'nutrition_builder', 'agenda_full', 'pdf_reports', 'ai_assistance', 'ai_training', 'ai_nutrition', 'create_workout_model'],
            ],
            [
                'name'         => 'Profissional Expert / Premium',
                'description'  => 'Recursos ilimitados, White-Label e automação total para profissionais de elite.',
                'type'         => 'professional',
                'price'        => 119.90,
                'ai_credits'   => 15000,
                'max_patients' => 9999,
                'trial_days'   => 14,
                'is_active'    => true,
                'roles'        => ['personal', 'nutricionista'],
                'features'     => ['patient_management', 'create_workout', 'training_builder', 'nutrition_builder', 'agenda_full', 'pdf_reports', 'ai_unlimited', 'white_label', 'marketing_tools', 'ai_training', 'ai_nutrition', 'create_workout_model', 'automated_actions'],
            ],
        ];

        // --- PLANOS PARA CLÍNICAS / ACADEMIAS (B2B) ---
        $clinicPlans = [
            [
                'name'              => 'Basic / Estúdio',
                'description'       => 'Gestão essencial para pequenos estúdios e clínicas multidisciplinares.',
                'type'              => 'clinic',
                'price'             => 197.00,
                'max_patients'      => 100,
                'max_professionals' => 3,
                'ai_credits'        => 5000,
                'trial_days'        => 30,
                'is_corporate'      => true,
                'is_active'         => true,
                'roles'             => ['academia'],
                'features'          => ['clinic_dashboard', 'financial_management', 'multi_professional', 'basic_crm'],
            ],
            [
                'name'              => 'Business / Clinic',
                'description'       => 'Controle avançado de alunos, profissionais e relatórios gerenciais.',
                'type'              => 'clinic',
                'price'             => 397.00,
                'max_patients'      => 500,
                'max_professionals' => 10,
                'ai_credits'        => 15000,
                'trial_days'        => 30,
                'is_corporate'      => true,
                'is_active'         => true,
                'roles'             => ['academia'],
                'features'          => ['clinic_dashboard', 'financial_management', 'multi_professional', 'advanced_crm', 'sales_funnel', 'ai_manager'],
            ],
            [
                'name'              => 'Enterprise',
                'description'       => 'Escala ilimitada para grandes academias e redes de clínicas.',
                'type'              => 'clinic',
                'price'             => 897.00,
                'max_patients'      => 99999,
                'max_professionals' => 999,
                'ai_credits'        => 50000,
                'trial_days'        => 30,
                'is_corporate'      => true,
                'is_active'         => true,
                'roles'             => ['academia'],
                'features'          => ['clinic_dashboard', 'financial_management', 'multi_professional', 'advanced_crm', 'custom_branding', 'api_access', 'audit_logs'],
            ],
        ];

        // --- CRIAR TODOS OS PLANOS ---
        $allGroups = [
            $studentPlans,
            $proPlans,
            $clinicPlans,
        ];

        foreach ($allGroups as $group) {
            foreach ($group as $p) {
                $plan = Plan::create([
                    'name'                   => $p['name'],
                    'description'            => $p['description'],
                    'type'                   => $p['type'],
                    'price'                  => $p['price'],
                    'ai_credits'             => $p['ai_credits'] ?? 0,
                    'max_workouts'           => $p['max_workouts'] ?? 0,
                    'max_diets'              => $p['max_diets'] ?? 0,
                    'max_assessments'        => $p['max_assessments'] ?? 0,
                    'max_patients'           => $p['max_patients'] ?? 0,
                    'max_professionals'      => $p['max_professionals'] ?? 0,
                    'price_per_professional' => $p['price_per_professional'] ?? 0,
                    'min_professionals'      => $p['min_professionals'] ?? 1,
                    'is_corporate'           => $p['is_corporate'] ?? false,
                    'trial_days'             => $p['trial_days'] ?? 0,
                    'commission_rate'        => $p['commission_rate'] ?? 10.00,
                    'is_active'              => $p['is_active'],
                ]);

                $this->seedFeatures($plan, array_fill_keys($p['features'], true));

                if (! empty($p['roles'])) {
                    $roleIds = Role::whereIn('name', $p['roles'])->pluck('id');
                    $plan->roles()->sync($roleIds);
                }
            }
        }

        // --- AI CREDIT PACKAGES ---
        $packages = [
            ['name' => 'Pacote 5k Créditos',  'credits' => 5000,  'price' => 49.90,  'is_active' => true],
            ['name' => 'Pacote 10k Créditos', 'credits' => 10000, 'price' => 89.90,  'is_active' => true],
            ['name' => 'Pacote 20k Créditos', 'credits' => 20000, 'price' => 159.90, 'is_active' => true],
        ];

        foreach ($packages as $pkg) {
            \App\Models\AiCreditPackage::updateOrCreate(
                ['credits' => $pkg['credits']],
                $pkg
            );
        }
    }

    private function seedFeatures(Plan $plan, array $features): void
    {
        foreach ($features as $key => $enabled) {
            \App\Models\PlanFeature::create([
                'plan_id'     => $plan->id,
                'feature_key' => $key,
                'is_enabled'  => $enabled,
            ]);
        }
    }
}
