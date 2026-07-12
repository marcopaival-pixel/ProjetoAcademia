<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1TrainingPlansTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
    }

    private function studentUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('aluno');
        $permission = Permission::firstOrCreate(
            ['name' => 'portal.access'],
            ['label' => 'Acesso ao Portal Usuário']
        );
        $user->permissions()->syncWithoutDetaching([$permission->id]);

        return $user;
    }

    public function test_training_plans_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/training-plans')->assertUnauthorized();
    }

    public function test_training_plans_index_returns_own_plans_for_student(): void
    {
        $user = $this->studentUser();
        $plan = TrainingPlan::create([
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'name' => 'Plano API',
            'is_active' => true,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/training-plans')
            ->assertOk()
            ->assertJsonPath('data.0.id', $plan->id)
            ->assertJsonPath('data.0.name', 'Plano API');
    }

    public function test_training_plans_show_returns_detail(): void
    {
        $user = $this->studentUser();
        $plan = TrainingPlan::create([
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'name' => 'Detalhe API',
            'is_active' => true,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/training-plans/'.$plan->id)
            ->assertOk()
            ->assertJsonPath('data.id', $plan->id)
            ->assertJsonStructure(['data' => ['exercises']]);
    }

    public function test_training_plans_show_denies_other_users_plan(): void
    {
        $owner = $this->studentUser();
        $other = $this->studentUser();
        $plan = TrainingPlan::create([
            'user_id' => $owner->id,
            'creator_id' => $owner->id,
            'name' => 'Privado',
            'is_active' => true,
        ]);

        Sanctum::actingAs($other);

        $this->getJson('/api/v1/training-plans/'.$plan->id)->assertForbidden();
    }

    public function test_student_can_store_load_log_for_own_plan_exercise(): void
    {
        $user = $this->studentUser();
        $plan = TrainingPlan::create([
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'name' => 'Carga API',
            'is_active' => true,
        ]);
        $exercise = \App\Models\ExerciseCatalog::create([
            'name' => 'Supino',
            'muscle_group' => 'Peito',
        ]);
        $planExercise = TrainingPlanExercise::create([
            'training_plan_id' => $plan->id,
            'exercise_id' => $exercise->id,
            'position' => 1,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/load-logs', [
            'training_plan_exercise_id' => $planExercise->id,
            'exercise_id' => $exercise->id,
            'log_date' => now()->toDateString(),
            'set_number' => 1,
            'reps_done' => 10,
            'weight_kg' => 40,
            'rpe' => 8,
        ])
            ->assertCreated()
            ->assertJsonPath('data.reps_done', 10)
            ->assertJsonPath('data.weight_kg', 40);

        $this->assertDatabaseHas('load_logs', [
            'user_id' => $user->id,
            'training_plan_exercise_id' => $planExercise->id,
            'reps_done' => 10,
        ]);
    }

    public function test_load_log_denies_other_users_plan_exercise(): void
    {
        $owner = $this->studentUser();
        $other = $this->studentUser();
        $plan = TrainingPlan::create([
            'user_id' => $owner->id,
            'creator_id' => $owner->id,
            'name' => 'Privado Carga',
            'is_active' => true,
        ]);
        $exercise = \App\Models\ExerciseCatalog::create([
            'name' => 'Remada',
            'muscle_group' => 'Costas',
        ]);
        $planExercise = TrainingPlanExercise::create([
            'training_plan_id' => $plan->id,
            'exercise_id' => $exercise->id,
            'position' => 1,
        ]);

        Sanctum::actingAs($other);

        $this->postJson('/api/v1/load-logs', [
            'training_plan_exercise_id' => $planExercise->id,
            'exercise_id' => $exercise->id,
            'log_date' => now()->toDateString(),
            'set_number' => 1,
            'reps_done' => 8,
            'weight_kg' => 35,
        ])->assertForbidden();
    }
}
