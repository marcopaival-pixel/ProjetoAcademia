<?php

namespace Tests\Feature;

use App\Models\FoodEntry;
use App\Models\BodyAssessment;
use App\Models\MealTemplate;
use App\Models\MealTemplateItem;
use App\Models\Role;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\WaterEntry;
use App\Models\WorkoutSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1NutritionAndSessionsTest extends TestCase
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

        return $user;
    }

    public function test_nutrition_diary_requires_authentication(): void
    {
        $this->getJson('/api/v1/nutrition/diary')->assertUnauthorized();
    }

    public function test_nutrition_diary_returns_entries_for_date(): void
    {
        $user = $this->studentUser();
        FoodEntry::create([
            'user_id' => $user->id,
            'entry_date' => now()->toDateString(),
            'meal_type' => 'lunch',
            'food_name' => 'Frango',
            'calories' => 250,
            'protein_g' => 30,
            'carbs_g' => 0,
            'fat_g' => 8,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/nutrition/diary?date='.now()->toDateString())
            ->assertOk()
            ->assertJsonPath('data.totals.calories', 250)
            ->assertJsonCount(1, 'data.entries');
    }

    public function test_workout_sessions_store_and_list(): void
    {
        $user = $this->studentUser();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/workout-sessions', [
            'session_date' => '2026-05-20',
            'rpe_score' => 8,
            'mood' => 'good',
            'notes' => 'Treino forte',
        ])
            ->assertCreated()
            ->assertJsonPath('data.rpe_score', 8);

        $this->getJson('/api/v1/workout-sessions')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->assertDatabaseHas('workout_sessions', [
            'user_id' => $user->id,
            'rpe_score' => 8,
        ]);
    }

    public function test_workout_session_active_lifecycle(): void
    {
        $user = $this->studentUser();
        $plan = TrainingPlan::create([
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'name' => 'Treino ativo',
            'is_active' => true,
        ]);
        Sanctum::actingAs($user);

        $sessionId = $this->postJson('/api/v1/workout-sessions/start', [
            'training_plan_id' => $plan->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.training_plan_id', $plan->id)
            ->json('data.id');

        $this->getJson('/api/v1/workout-sessions/active')
            ->assertOk()
            ->assertJsonPath('data.id', $sessionId);

        $this->patchJson('/api/v1/workout-sessions/'.$sessionId, [
            'status' => 'paused',
            'completion_percent' => 50,
            'completed_exercise_ids' => [10, 20],
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'paused')
            ->assertJsonPath('data.completion_percent', 50)
            ->assertJsonPath('data.completed_exercise_ids.0', 10);

        $this->patchJson('/api/v1/workout-sessions/'.$sessionId, [
            'status' => 'completed',
            'completion_percent' => 100,
            'rpe_score' => 8,
            'notes' => 'Finalizado pelo app',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.rpe_score', 8);

        $this->getJson('/api/v1/workout-sessions/active')
            ->assertOk()
            ->assertJsonPath('data', null);
    }

    public function test_hydration_status_store_and_delete(): void
    {
        $user = $this->studentUser();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/hydration/status?date='.now()->toDateString())
            ->assertOk()
            ->assertJsonPath('data.consumed_ml', 0);

        $entryId = $this->postJson('/api/v1/hydration/entries', [
            'amount_ml' => 350,
            'source' => 'test',
        ])
            ->assertCreated()
            ->assertJsonPath('data.amount_ml', 350)
            ->json('data.id');

        $this->getJson('/api/v1/hydration/status?date='.now()->toDateString())
            ->assertOk()
            ->assertJsonPath('data.consumed_ml', 350)
            ->assertJsonCount(1, 'data.entries');

        $this->deleteJson('/api/v1/hydration/entries/'.$entryId)
            ->assertOk()
            ->assertJsonPath('data.deleted', true);
    }

    public function test_hydration_delete_denies_other_users_entry(): void
    {
        $owner = $this->studentUser();
        $other = $this->studentUser();
        $entry = WaterEntry::create([
            'user_id' => $owner->id,
            'entry_date' => now()->toDateString(),
            'drank_at' => now(),
            'amount_ml' => 250,
            'source' => 'test',
        ]);

        Sanctum::actingAs($other);

        $this->deleteJson('/api/v1/hydration/entries/'.$entry->id)->assertForbidden();
    }

    public function test_meal_templates_list_and_apply_to_diary(): void
    {
        $user = $this->studentUser();
        $template = MealTemplate::create([
            'user_id' => $user->id,
            'name' => 'Plano base',
        ]);
        MealTemplateItem::create([
            'meal_template_id' => $template->id,
            'meal_type' => 'breakfast',
            'food_name' => 'Ovos',
            'calories' => 180,
            'protein_g' => 14,
            'carbs_g' => 2,
            'fat_g' => 12,
            'position' => 1,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/nutrition/meal-templates')
            ->assertOk()
            ->assertJsonPath('data.templates.0.name', 'Plano base')
            ->assertJsonPath('data.templates.0.items.0.food_name', 'Ovos');

        $this->postJson('/api/v1/nutrition/meal-templates/'.$template->id.'/apply', [
            'entry_date' => '2026-07-10',
        ])
            ->assertOk()
            ->assertJsonPath('data.applied', 1);

        $this->assertDatabaseHas('food_entries', [
            'user_id' => $user->id,
            'entry_date' => '2026-07-10',
            'food_name' => 'Ovos',
        ]);
    }

    public function test_meal_template_apply_denies_other_users_template(): void
    {
        $owner = $this->studentUser();
        $other = $this->studentUser();
        $template = MealTemplate::create([
            'user_id' => $owner->id,
            'name' => 'Privado',
        ]);

        Sanctum::actingAs($other);

        $this->postJson('/api/v1/nutrition/meal-templates/'.$template->id.'/apply', [
            'entry_date' => '2026-07-10',
        ])->assertForbidden();
    }

    public function test_assessment_summary_returns_goal_and_deltas(): void
    {
        $user = $this->studentUser();
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['height_cm' => 180, 'target_weight_kg' => 80]
        );
        BodyAssessment::create([
            'user_id' => $user->id,
            'assessment_date' => '2026-06-01',
            'weight_kg' => 90,
            'bf_percent' => 25,
            'muscle_percent' => 35,
            'status' => 'approved',
        ]);
        BodyAssessment::create([
            'user_id' => $user->id,
            'assessment_date' => '2026-07-01',
            'weight_kg' => 86,
            'bf_percent' => 23,
            'muscle_percent' => 36,
            'status' => 'approved',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/assessments/summary')
            ->assertOk()
            ->assertJsonPath('data.current_weight_kg', 86)
            ->assertJsonPath('data.initial_weight_kg', 90)
            ->assertJsonPath('data.target_weight_kg', 80)
            ->assertJsonPath('data.bmi', 26.5)
            ->assertJsonPath('data.deltas.weight_kg', -4)
            ->assertJsonPath('data.goal_progress_percent', 40);
    }
}
