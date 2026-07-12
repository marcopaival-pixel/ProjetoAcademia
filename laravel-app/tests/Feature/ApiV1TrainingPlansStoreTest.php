<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1TrainingPlansStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
    }

    private function studentUser(bool $isPremium = false): User
    {
        $user = User::factory()->create([
            'is_premium' => $isPremium,
            'premium_expires_at' => $isPremium ? now()->addYear() : null,
        ]);
        $user->assignRole('aluno');

        $permission = Permission::firstOrCreate(
            ['name' => 'portal.access'],
            ['label' => 'Acesso ao Portal Usuário']
        );
        $user->permissions()->syncWithoutDetaching([$permission->id]);

        return $user;
    }

    public function test_training_plans_store_requires_authentication(): void
    {
        $this->postJson('/api/v1/training-plans', [
            'name' => 'Plano Teste',
        ])->assertUnauthorized();
    }

    public function test_premium_student_can_create_training_plan(): void
    {
        $user = $this->studentUser(isPremium: true);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/training-plans', [
            'name' => 'Meu Plano Novo',
            'goal' => 'Hipertrofia',
            'description' => 'Treino focado em ganho de massa muscular.',
            'frequency' => 4,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Meu Plano Novo');

        $this->assertDatabaseHas('training_plans', [
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'professional_id' => null,
            'name' => 'Meu Plano Novo',
            'goal' => 'Hipertrofia',
        ]);
    }

    public function test_free_student_cannot_create_training_plan(): void
    {
        $user = $this->studentUser(isPremium: false);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/training-plans', [
            'name' => 'Meu Plano Novo',
        ])->assertStatus(403);
    }
}
