<?php

namespace Tests\Feature;

use App\Models\FoodEntry;
use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1NutritionAndSessionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_nutrition_diary_requires_authentication(): void
    {
        $this->getJson('/api/v1/nutrition/diary')->assertUnauthorized();
    }

    public function test_nutrition_diary_returns_entries_for_date(): void
    {
        $user = User::factory()->create();
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
            ->assertJsonPath('totals.calories', 250)
            ->assertJsonCount(1, 'entries');
    }

    public function test_workout_sessions_store_and_list(): void
    {
        $user = User::factory()->create();
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
}
