<?php

namespace App\Services;

use App\Models\AcademyCompany;
use App\Models\BodyAssessment;
use App\Models\ExerciseEntry;
use App\Models\ExerciseCatalog;
use App\Models\FoodEntry;
use App\Models\HealthAlert;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TrainingPlan;
use App\Models\UserProfile;
use App\Models\User;
use App\Models\WaterEntry;
use App\Models\WeightEntry;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoDataService
{
    /**
     * Prepara o ambiente de demonstração para um perfil específico.
     */
    public function setupDemoEnvironment(string $profile, ?User $user = null): User
    {
        $demoUser = $user ?? Auth::user();

        if (! $demoUser || ! $demoUser->is_demo) {
            // Se não houver usuário, buscar ou criar o usuário demo
            $demoUser = User::where('email', 'demo@nexshape.com.br')->first();

            if (! $demoUser) {
                $company = AcademyCompany::first();

                $demoUser = new User;
                $demoUser->fill([
                    'email' => 'demo@nexshape.com.br',
                    'name' => 'Usuário Demonstração',
                    'username' => 'demo_user',
                    'is_demo' => true,
                    'is_premium' => true,
                    'academy_company_id' => $company ? $company->id : null,
                    'status' => 'active',
                    'email_verified' => true,
                    'plan_id' => 2, // Premium
                    'professional_plan_id' => 3, // Profissional Premium
                ]);
                $demoUser->setPlainPassword(Str::password(16), false);
            }
        }

        $demoUser->force_password_change = false;
        $demoUser->temp_password_expires_at = null;

        // Atribuir papel correto e remover o anterior para evitar conflito de redirecionamento
        if ($profile === 'aluno' || $profile === 'student') {
            $demoUser->assignRole('aluno');
            $demoUser->removeRole('professional');
            $demoUser->removeRole('manager');
            $demoUser->is_admin = false;
            $profile = 'student';
        } elseif ($profile === 'clinic' || $profile === 'gestor') {
            $demoUser->assignRole('manager');
            $demoUser->removeRole('aluno');
            $demoUser->removeRole('professional');
            $demoUser->is_admin = true; // Necessário para acessar rotas do painel admin/clínica
            $profile = 'clinic';

            // Garantir permissões críticas para demo gestor
            $permissions = Permission::whereIn('name', ['portal.access', 'admin.access'])->get();
            $demoUser->permissions()->syncWithoutDetaching($permissions->pluck('id'));

            // Limpar cache de permissões para que a mudança seja imediata
            Cache::forget("user_permissions_v2_{$demoUser->id}");
        } else {
            $demoUser->assignRole('professional');
            $demoUser->removeRole('aluno');
            $demoUser->removeRole('manager');
            $demoUser->is_admin = false;
            $profile = 'professional';
        }

        $demoUser->save();

        // Forçar is_admin via DB direto para evitar observers ou travas de modelo
        DB::table('users')
            ->where('id', $demoUser->id)
            ->update(['is_admin' => ($profile === 'clinic' ? 1 : 0)]);

        // Limpar cache global e específico para garantir leitura fresca
        Artisan::call('cache:clear');
        Cache::forget("user_permissions_v2_{$demoUser->id}");

        // Limpar dados anteriores de demo deste usuário para um "Fresh Start"
        $this->clearDemoData($demoUser);

        // 3. Gerar massa de dados baseada no perfil
        $this->generateMockData($demoUser, $profile);
        $this->generateStudentExperience($demoUser, $profile === 'professional' ? $demoUser : null);

        return $demoUser;
    }

    public function clearDemoData(User $user)
    {
        // Remove apenas dados vinculados ao usuário demo
        $user->foodEntries()->delete();
        $user->exerciseEntries()->delete();
        $user->weightEntries()->delete();
        $user->waterEntries()->delete();
        $user->trainingPlans()->delete();
        BodyAssessment::where('user_id', $user->id)->delete();
        HealthAlert::where('user_id', $user->id)->delete();
    }

    private function generateMockData(User $user, $profile)
    {
        if ($profile === 'professional') {
            // Criar um aluno demonstrativo para o profissional testar
            $patient = User::where('email', 'aluno_demo@nexshape.com.br')->first();
            if (! $patient) {
                $patient = new User;
                $patient->fill([
                    'email' => 'aluno_demo@nexshape.com.br',
                    'name' => 'Aluno Demonstração',
                    'username' => 'aluno_demo',
                    'is_demo' => true,
                    'is_premium' => true,
                    'status' => 'active',
                    'email_verified' => true,
                    'plan_id' => 2,
                    'academy_company_id' => $user->academy_company_id,
                ]);
                $patient->setPlainPassword(Str::password(16), false);

                $role = Role::where('name', 'aluno')->first();
                if ($role) {
                    $patient->roles()->syncWithoutDetaching([$role->id]);
                }
            }

            $patient->force_password_change = false;
            $patient->temp_password_expires_at = null;
            $patient->save();

            // Vincular o aluno ao profissional
            if (! $user->patients()->where('users.id', $patient->id)->exists()) {
                $user->patients()->attach($patient->id, [
                    'status' => 'Sim',
                    'data_cadastro' => now(),
                ]);
            }

            $this->clearDemoData($patient);
            $this->generateStudentExperience($patient, $user);
        }
    }

    private function generateStudentExperience(User $user, ?User $professional = null): void
    {
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'birth_date' => now()->subYears(29)->toDateString(),
                'sex' => 'M',
                'height_cm' => 178,
                'activity_level' => 'active',
                'goal' => 'recomp',
                'daily_calorie_target' => 2450,
                'protein_target_g' => 165,
                'carbs_target_g' => 265,
                'fat_target_g' => 70,
                'water_target_ml' => 3000,
                'is_water_target_auto' => false,
                'target_weight_kg' => 78,
                'training_days_per_week' => 5,
                'physical_level' => 'intermediate',
                'experience_level' => 'intermediate',
                'training_location' => 'gym',
                'cardio_frequency' => '2x_week',
                'sleep_hours' => 7,
                'nutrition_quality' => 8,
                'available_daily_time_mins' => 70,
                'fitness_notes' => 'Demo: foco em recomposição corporal com melhora de força e constância.',
                'profile_completed_at' => now(),
            ]
        );

        $today = now()->toDateString();

        foreach ([
            ['breakfast', 'Ovos mexidos com pão integral', 1, 'prato', 420, 28, 38, 18],
            ['lunch', 'Frango grelhado, arroz e salada', 1, 'prato', 680, 48, 72, 18],
            ['snack', 'Iogurte proteico com banana', 1, 'unidade', 310, 26, 42, 5],
            ['dinner', 'Salmão, batata doce e legumes', 1, 'prato', 590, 42, 46, 22],
        ] as [$meal, $name, $amount, $unit, $calories, $protein, $carbs, $fat]) {
            FoodEntry::create([
                'user_id' => $user->id,
                'entry_date' => $today,
                'meal_type' => $meal,
                'food_name' => $name,
                'amount' => $amount,
                'unit' => $unit,
                'calories' => $calories,
                'protein_g' => $protein,
                'carbs_g' => $carbs,
                'fat_g' => $fat,
            ]);
        }

        foreach ([700, 600, 500, 650] as $index => $amount) {
            WaterEntry::create([
                'user_id' => $user->id,
                'entry_date' => $today,
                'drank_at' => now()->startOfDay()->addHours(8 + ($index * 3)),
                'amount_ml' => $amount,
                'source' => 'demo_seed',
            ]);
        }

        foreach ([82.4, 81.6, 80.8, 79.9] as $index => $weight) {
            WeightEntry::create([
                'user_id' => $user->id,
                'weighed_at' => now()->subWeeks(3 - $index)->toDateString(),
                'weight_kg' => $weight,
            ]);
        }

        ExerciseEntry::create([
            'user_id' => $user->id,
            'entry_date' => $today,
            'activity_type' => 'Treino Superior A',
            'duration_min' => 58,
            'calories_burned' => 420,
            'sets_data' => [
                ['exercise' => 'Supino reto', 'sets' => 4, 'reps' => '8-10'],
                ['exercise' => 'Remada baixa', 'sets' => 4, 'reps' => '10-12'],
                ['exercise' => 'Desenvolvimento', 'sets' => 3, 'reps' => '10'],
            ],
            'notes' => 'Demo: sessão concluída com boa percepção de esforço.',
        ]);

        $plan = TrainingPlan::create([
            'user_id' => $user->id,
            'professional_id' => $professional?->id,
            'creator_id' => $professional?->id,
            'name' => 'Plano Demo - Recomposição',
            'plan_label' => 'ABC',
            'description' => 'Plano demonstrativo com foco em força, hipertrofia e aderência semanal.',
            'goal' => 'Recomposição corporal',
            'frequency' => 5,
            'difficulty' => 'Intermediário',
            'estimated_duration' => 60,
            'is_active' => true,
            'student_profile' => 'Intermediário',
            'split_type' => 'ABC',
            'status' => 'active',
            'days_of_week' => ['segunda', 'terça', 'quinta', 'sexta', 'sábado'],
            'is_template' => false,
            'total_volume' => 36,
            'muscles_worked' => ['peito', 'costas', 'pernas', 'ombros'],
            'created_by_ai' => false,
        ]);

        foreach ([
            ['Supino reto com barra', 'Peitoral', 1, '4x8-10 | descanso 90s'],
            ['Remada baixa', 'Costas', 2, '4x10-12 | controle total'],
            ['Agachamento livre', 'Pernas', 3, '4x8 | progressão semanal'],
            ['Elevação lateral', 'Ombros', 4, '3x12-15 | execução lenta'],
        ] as [$name, $muscleGroup, $position, $notes]) {
            $catalogExercise = ExerciseCatalog::firstOrCreate(
                ['name' => $name],
                [
                    'muscle_group' => $muscleGroup,
                    'equipment' => 'Academia',
                    'difficulty' => 'Intermediário',
                    'instructions' => 'Exercício demonstrativo usado no modo demo.',
                    'is_active' => true,
                ]
            );

            $plan->exercises()->create([
                'exercise_id' => $catalogExercise->id,
                'custom_name' => $name,
                'position' => $position,
                'notes' => $notes,
            ]);
        }

        BodyAssessment::create([
            'user_id' => $user->id,
            'professional_id' => $professional?->id,
            'created_by' => $professional ? 'professional' : 'patient',
            'weight_kg' => 79.9,
            'bf_percent' => 18.4,
            'muscle_percent' => 42.1,
            'waist' => 83,
            'abdomen' => 86,
            'hips' => 98,
            'assessment_date' => now()->subDays(4)->toDateString(),
            'status' => 'approved',
            'notes' => 'Demo: boa evolução de cintura e manutenção de massa magra.',
            'ai_suggestions' => [
                'Manter proteína acima de 160g/dia.',
                'Progredir carga em exercícios base.',
                'Adicionar 2 sessões leves de cardio.',
            ],
            'visceral_fat_level' => 7,
            'basal_metabolic_rate' => 1810,
        ]);

        HealthAlert::create([
            'user_id' => $user->id,
            'type' => 'adherence',
            'severity' => 'medium',
            'message' => 'Demo: aluno manteve 82% de aderência nos últimos 7 dias.',
            'is_read' => false,
        ]);
    }
}
