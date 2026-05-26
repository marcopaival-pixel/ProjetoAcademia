<?php

namespace Tests\Feature;

use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1TrainingPlansTest extends TestCase
{
    use RefreshDatabase;

    public function test_training_plans_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/training-plans')->assertUnauthorized();
    }

    public function test_training_plans_index_returns_own_plans_for_admin_user(): void
    {
        $user = User::factory()->administrator()->create();
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
        $user = User::factory()->administrator()->create();
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
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $plan = TrainingPlan::create([
            'user_id' => $owner->id,
            'creator_id' => $owner->id,
            'name' => 'Privado',
            'is_active' => true,
        ]);

        Sanctum::actingAs($other);

        $this->getJson('/api/v1/training-plans/'.$plan->id)->assertForbidden();
    }
}
