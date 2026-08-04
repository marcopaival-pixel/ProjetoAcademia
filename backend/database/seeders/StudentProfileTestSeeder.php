<?php

namespace Database\Seeders;

use App\Models\AcademyCompany;
use App\Models\BodyAssessment;
use App\Models\FoodEntry;
use App\Models\Plan;
use App\Models\ProfessionalPatient;
use App\Models\Subscription;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WaterEntry;
use App\Models\WeightEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Cria os 9 perfis de aluno descritos no plano de testes finais (seção 2).
 *
 * Uso:
 *   php artisan db:seed --class=StudentProfileTestSeeder
 *
 * Senha padrão de todos os usuários QA: Teste@123
 */
class StudentProfileTestSeeder extends Seeder
{
    public const QA_PASSWORD = 'Teste@123';

    /** @var array<string, array{email: string, name: string, scenario: string}> */
    public const PROFILES = [
        'free' => [
            'email' => 'aluno.free@test.nexshape',
            'name' => 'QA — Aluno Free (sem vínculo)',
            'scenario' => 'Aluno Free sem vínculo com profissional',
        ],
        'premium' => [
            'email' => 'aluno.premium@test.nexshape',
            'name' => 'QA — Aluno Premium (sem vínculo)',
            'scenario' => 'Aluno Premium sem vínculo',
        ],
        'linked_pro' => [
            'email' => 'aluno.vinculado.pro@test.nexshape',
            'name' => 'QA — Aluno vinculado ao profissional',
            'scenario' => 'Aluno vinculado a um profissional',
        ],
        'linked_clinic' => [
            'email' => 'aluno.vinculado.clinica@test.nexshape',
            'name' => 'QA — Aluno vinculado à clínica',
            'scenario' => 'Aluno vinculado a uma clínica',
        ],
        'aluno_paciente' => [
            'email' => 'aluno.paciente@test.nexshape',
            'name' => 'QA — Aluno + Paciente',
            'scenario' => 'Aluno que também possui perfil de paciente',
        ],
        'expired' => [
            'email' => 'aluno.expirado@test.nexshape',
            'name' => 'QA — Aluno assinatura expirada',
            'scenario' => 'Aluno com assinatura expirada',
        ],
        'incomplete' => [
            'email' => 'aluno.incompleto@test.nexshape',
            'name' => 'QA — Aluno cadastro incompleto',
            'scenario' => 'Aluno com cadastro incompleto',
        ],
        'new' => [
            'email' => 'aluno.novo@test.nexshape',
            'name' => 'QA — Aluno novo (sem histórico)',
            'scenario' => 'Aluno novo sem treinos ou registros',
        ],
        'complete' => [
            'email' => 'aluno.completo@test.nexshape',
            'name' => 'QA — Aluno histórico completo',
            'scenario' => 'Aluno com histórico completo de treinos, alimentação e avaliações',
        ],
    ];

    public function run(): void
    {
        $this->ensureDependencies();

        $freePlan = Plan::where('name', 'Free')->where('type', 'student')->first();
        $premiumPlan = Plan::where('name', 'Premium')->where('type', 'student')->first();

        if (! $freePlan || ! $premiumPlan) {
            $this->command?->error('Planos Free/Premium não encontrados. Execute: php artisan db:seed --class=PlanSeeder');

            return;
        }

        $clinic = AcademyCompany::where('slug', 'principal')->first()
            ?? AcademyCompany::first();

        $professional = $this->ensureQaProfessional($clinic);

        $this->seedFreeStudent(self::PROFILES['free'], $freePlan);
        $this->seedPremiumStudent(self::PROFILES['premium'], $premiumPlan, $clinic);
        $this->seedLinkedProfessionalStudent(self::PROFILES['linked_pro'], $freePlan, $professional, $clinic);
        $this->seedLinkedClinicStudent(self::PROFILES['linked_clinic'], $freePlan, $clinic);
        $this->seedAlunoPacienteStudent(self::PROFILES['aluno_paciente'], $freePlan, $clinic);
        $this->seedExpiredStudent(self::PROFILES['expired'], $premiumPlan, $clinic);
        $this->seedIncompleteStudent(self::PROFILES['incomplete'], $freePlan);
        $this->seedNewStudent(self::PROFILES['new'], $freePlan);
        $this->seedCompleteStudent(self::PROFILES['complete'], $premiumPlan, $professional, $clinic);

        $this->printCredentials($professional);
    }

    private function ensureDependencies(): void
    {
        if (! Plan::where('name', 'Free')->exists()) {
            $this->call(PlanSeeder::class);
        }

        if (! DB::table('roles')->where('name', 'aluno')->exists()) {
            $this->call(RolesAndPermissionsSeeder::class);
        }

        if (! AcademyCompany::exists()) {
            $this->call(AcademyCompanySeeder::class);
        }
    }

    private function ensureQaProfessional(?AcademyCompany $clinic): User
    {
        $email = 'prof.qa@test.nexshape';

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = new User();
            $user->fill([
                'email' => $email,
                'name' => 'QA — Profissional (vínculos de teste)',
                'uuid' => (string) Str::uuid(),
                'status' => 'active',
                'email_verified_at' => now(),
                'email_verified' => true,
                'registration_approval_status' => 'approved',
                'onboarding_status' => 'completed',
                'profile_completion_percentage' => 100,
                'academy_company_id' => $clinic?->id,
                'professional_code' => 'QA-PRO-001',
            ]);
            $user->password_hash = Hash::make(self::QA_PASSWORD);
            $user->save();
        }

        $user->assignRole('professional');

        if ($clinic) {
            $this->linkUserToClinic($user, $clinic, 'professional');
        }

        return $user;
    }

    private function seedFreeStudent(array $meta, Plan $freePlan): void
    {
        $user = $this->upsertStudent($meta, [
            'plan_id' => $freePlan->id,
            'is_premium' => false,
            'premium_expires_at' => null,
            'onboarding_status' => 'completed',
            'profile_completion_percentage' => 100,
        ]);

        $this->ensureCompleteProfile($user);
    }

    private function seedPremiumStudent(array $meta, Plan $premiumPlan, ?AcademyCompany $clinic): void
    {
        $user = $this->upsertStudent($meta, [
            'plan_id' => $premiumPlan->id,
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
            'onboarding_status' => 'completed',
            'profile_completion_percentage' => 100,
            'academy_company_id' => $clinic?->id,
        ]);

        $this->ensureCompleteProfile($user);
        $this->ensureActiveSubscription($user, $premiumPlan, $clinic);
    }

    private function seedLinkedProfessionalStudent(array $meta, Plan $freePlan, User $professional, ?AcademyCompany $clinic): void
    {
        $user = $this->upsertStudent($meta, [
            'plan_id' => $freePlan->id,
            'is_premium' => false,
            'onboarding_status' => 'completed',
            'profile_completion_percentage' => 100,
            'academy_company_id' => $clinic?->id,
        ]);

        $this->ensureCompleteProfile($user);
        $this->linkPatientToProfessional($user, $professional, $clinic);

        TrainingPlan::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Ficha QA — Prescrita pelo profissional'],
            [
                'professional_id' => $professional->id,
                'creator_id' => $professional->id,
                'description' => 'Plano de teste criado pelo profissional vinculado.',
                'is_active' => true,
                'status' => 'active',
            ]
        );
    }

    private function seedLinkedClinicStudent(array $meta, Plan $freePlan, ?AcademyCompany $clinic): void
    {
        $user = $this->upsertStudent($meta, [
            'plan_id' => $freePlan->id,
            'is_premium' => false,
            'onboarding_status' => 'completed',
            'profile_completion_percentage' => 100,
            'academy_company_id' => $clinic?->id,
        ]);

        $this->ensureCompleteProfile($user);

        if ($clinic) {
            $this->linkUserToClinic($user, $clinic, 'patient');
        }
    }

    private function seedAlunoPacienteStudent(array $meta, Plan $freePlan, ?AcademyCompany $clinic): void
    {
        $user = $this->upsertStudent($meta, [
            'plan_id' => $freePlan->id,
            'is_premium' => false,
            'onboarding_status' => 'completed',
            'profile_completion_percentage' => 100,
            'academy_company_id' => $clinic?->id,
            'perfil_paciente_completo' => true,
        ]);

        $this->ensureCompleteProfile($user);
        $user->assignRole('paciente');

        if ($clinic) {
            $this->linkUserToClinic($user, $clinic, 'patient');
        }
    }

    private function seedExpiredStudent(array $meta, Plan $premiumPlan, ?AcademyCompany $clinic): void
    {
        $freePlan = Plan::where('name', 'Free')->where('type', 'student')->first();

        $user = $this->upsertStudent($meta, [
            'plan_id' => $freePlan?->id ?? $premiumPlan->id,
            'is_premium' => false,
            'premium_expires_at' => now()->subDays(15),
            'onboarding_status' => 'completed',
            'profile_completion_percentage' => 100,
            'academy_company_id' => $clinic?->id,
        ]);

        $this->ensureCompleteProfile($user);

        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'plan_id' => $premiumPlan->id],
            [
                'academy_company_id' => $clinic?->id,
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => now()->subDays(15)->toDateString(),
                'status' => Subscription::STATUS_EXPIRED,
            ]
        );
    }

    private function seedIncompleteStudent(array $meta, Plan $freePlan): void
    {
        $user = $this->upsertStudent($meta, [
            'plan_id' => $freePlan->id,
            'is_premium' => false,
            'onboarding_status' => 'pending',
            'profile_completion_percentage' => 15,
        ]);

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['birth_date' => now()->subYears(25)->toDateString()]
        );
    }

    private function seedNewStudent(array $meta, Plan $freePlan): void
    {
        $user = $this->upsertStudent($meta, [
            'plan_id' => $freePlan->id,
            'is_premium' => false,
            'onboarding_status' => 'completed',
            'profile_completion_percentage' => 100,
        ]);

        $this->ensureCompleteProfile($user);

        $user->trainingPlans()->delete();
        $user->foodEntries()->delete();
        $user->waterEntries()->delete();
        $user->weightEntries()->delete();
        BodyAssessment::where('user_id', $user->id)->delete();
    }

    private function seedCompleteStudent(array $meta, Plan $premiumPlan, User $professional, ?AcademyCompany $clinic): void
    {
        $user = $this->upsertStudent($meta, [
            'plan_id' => $premiumPlan->id,
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
            'onboarding_status' => 'completed',
            'profile_completion_percentage' => 100,
            'academy_company_id' => $clinic?->id,
        ]);

        $this->ensureCompleteProfile($user);
        $this->ensureActiveSubscription($user, $premiumPlan, $clinic);
        $this->linkPatientToProfessional($user, $professional, $clinic);

        $user->trainingPlans()->delete();
        $user->foodEntries()->delete();
        $user->waterEntries()->delete();
        $user->weightEntries()->delete();
        BodyAssessment::where('user_id', $user->id)->delete();

        TrainingPlan::create([
            'user_id' => $user->id,
            'name' => 'Treino QA — Peito e Tríceps',
            'description' => 'Plano autônomo do aluno.',
            'is_active' => true,
            'status' => 'active',
        ]);

        TrainingPlan::create([
            'user_id' => $user->id,
            'professional_id' => $professional->id,
            'name' => 'Treino QA — Prescrito (histórico)',
            'description' => 'Ficha do profissional para testes de histórico.',
            'is_active' => true,
            'status' => 'active',
        ]);

        for ($d = 6; $d >= 0; $d--) {
            $date = now()->subDays($d)->toDateString();

            FoodEntry::create([
                'user_id' => $user->id,
                'academy_company_id' => $clinic?->id,
                'entry_date' => $date,
                'meal_type' => 'lunch',
                'food_name' => 'Frango grelhado + arroz integral',
                'amount' => 300,
                'unit' => 'g',
                'calories' => 520,
                'protein_g' => 45,
                'carbs_g' => 55,
                'fat_g' => 12,
            ]);

            WaterEntry::create([
                'user_id' => $user->id,
                'academy_company_id' => $clinic?->id,
                'entry_date' => $date,
                'drank_at' => now()->subDays($d)->setHour(10),
                'amount_ml' => 500,
                'source' => 'manual',
            ]);
        }

        for ($w = 8; $w >= 0; $w--) {
            WeightEntry::create([
                'user_id' => $user->id,
                'academy_company_id' => $clinic?->id,
                'weighed_at' => now()->subWeeks($w)->toDateString(),
                'weight_kg' => 78.5 - ($w * 0.2),
            ]);
        }

        BodyAssessment::create([
            'user_id' => $user->id,
            'professional_id' => $professional->id,
            'weight_kg' => 77.8,
            'bf_percent' => 18.5,
            'muscle_percent' => 42.0,
            'assessment_date' => now()->subMonth()->toDateString(),
            'status' => 'approved',
            'created_by' => 'professional',
            'notes' => 'Avaliação QA — baseline',
        ]);

        BodyAssessment::create([
            'user_id' => $user->id,
            'weight_kg' => 76.9,
            'bf_percent' => 17.2,
            'muscle_percent' => 43.1,
            'assessment_date' => now()->subWeek()->toDateString(),
            'status' => 'approved',
            'created_by' => 'patient',
            'notes' => 'Avaliação QA — acompanhamento',
        ]);
    }

    private function upsertStudent(array $meta, array $attributes): User
    {
        $user = User::where('email', $meta['email'])->first();

        $base = array_merge([
            'name' => $meta['name'],
            'uuid' => (string) Str::uuid(),
            'status' => 'active',
            'email_verified_at' => now(),
            'email_verified' => true,
            'registration_approval_status' => 'approved',
            'is_admin' => false,
            'is_demo' => false,
        ], $attributes);

        if ($user) {
            $user->fill($base);
            if (! $user->password_hash) {
                $user->password_hash = Hash::make(self::QA_PASSWORD);
            }
            $user->save();
        } else {
            $user = new User();
            $user->fill(array_merge($base, ['email' => $meta['email']]));
            $user->password_hash = Hash::make(self::QA_PASSWORD);
            $user->save();
        }

        $user->assignRole('aluno');

        return $user->fresh();
    }

    private function ensureCompleteProfile(User $user): void
    {
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'birth_date' => now()->subYears(28)->toDateString(),
                'sex' => 'M',
                'height_cm' => 175,
                'activity_level' => 'moderate',
                'goal' => 'gain',
                'target_weight_kg' => 80,
                'training_days_per_week' => 4,
                'water_target_ml' => 2500,
                'profile_completed_at' => now(),
            ]
        );

        WeightEntry::firstOrCreate(
            [
                'user_id' => $user->id,
                'weighed_at' => now()->toDateString(),
            ],
            [
                'weight_kg' => 78.0,
                'academy_company_id' => $user->academy_company_id,
            ]
        );
    }

    private function ensureActiveSubscription(User $user, Plan $plan, ?AcademyCompany $clinic): void
    {
        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'status' => Subscription::STATUS_ACTIVE],
            [
                'plan_id' => $plan->id,
                'academy_company_id' => $clinic?->id,
                'start_date' => now()->subWeek()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
            ]
        );
    }

    private function linkPatientToProfessional(User $patient, User $professional, ?AcademyCompany $clinic): void
    {
        ProfessionalPatient::updateOrCreate(
            [
                'profissional_id' => $professional->id,
                'user_id' => $patient->id,
            ],
            [
                'status' => 'Sim',
                'empresa_id' => $clinic?->id,
                'data_cadastro' => now(),
                'patient_permissions' => [
                    'view_training' => true,
                    'view_nutrition' => true,
                    'view_assessments' => true,
                ],
            ]
        );
    }

    private function linkUserToClinic(User $user, AcademyCompany $clinic, string $role): void
    {
        DB::table('clinic_user')->updateOrInsert(
            [
                'user_id' => $user->id,
                'academy_company_id' => $clinic->id,
                'role' => $role,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function printCredentials(User $professional): void
    {
        if (! $this->command) {
            return;
        }

        $this->command->newLine();
        $this->command->info('=== Usuários QA — Perfil Aluno ===');
        $this->command->info('Senha de todos: '.self::QA_PASSWORD);
        $this->command->newLine();

        $rows = [];
        foreach (self::PROFILES as $key => $profile) {
            $rows[] = [$key, $profile['email'], $profile['scenario']];
        }
        $rows[] = ['profissional', $professional->email, 'Profissional para vínculos/agenda'];

        $this->command->table(['Chave', 'E-mail', 'Cenário'], $rows);

        $this->command->newLine();
        $this->command->line('Web:  http://localhost:8000/login');
        $this->command->line('Demo: http://localhost:8000/demo/start?profile=aluno (demo@nexshape.com.br / demo123)');
        $this->command->line('API:  POST /api/v1/auth/token');
        $this->command->newLine();
        $this->command->line('Smoke: php artisan app:api:smoke --url=http://localhost:8000 --email=aluno.completo@test.nexshape --password='.self::QA_PASSWORD);
    }
}
