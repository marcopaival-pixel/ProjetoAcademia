<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\ActiveRestRoutine;
use App\Models\ActiveRestFavorite;
use App\Models\ActiveRestLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1StudentActiveRestTest extends TestCase
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

    private function createRoutine(array $attributes = []): ActiveRestRoutine
    {
        return ActiveRestRoutine::create(array_merge([
            'title' => 'Alongamento Matinal',
            'category' => 'Mobilidade',
            'duration' => 15,
            'intensity' => 'Leve',
            'recommended_level' => 'Iniciante',
            'benefit' => 'Melhora flexibilidade',
            'exercises' => [],
            'execution_steps' => [],
            'tips' => [],
            'common_errors' => [],
            'is_premium' => false,
            'is_active' => true,
            'order' => 1,
        ], $attributes));
    }

    public function test_active_rest_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/student/active-rest')->assertUnauthorized();
    }

    public function test_student_can_fetch_active_rest_data(): void
    {
        $student = $this->studentUser();
        $student->forceFill([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ])->save();
        $this->createRoutine();

        Sanctum::actingAs($student);

        $response = $this->getJson('/api/v1/student/active-rest')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'is_off_day',
                    'suggested_routine_id',
                    'routines' => [
                        '*' => [
                            'id',
                            'title',
                            'category',
                            'duration',
                            'intensity',
                            'recommended_level',
                            'benefit',
                            'is_premium',
                            'exercises',
                            'execution_steps',
                            'tips',
                            'common_errors',
                            'is_favorite',
                        ]
                    ]
                ]
            ]);

        $response->assertJsonCount(1, 'data.routines');
    }

    public function test_student_can_toggle_favorite_routine(): void
    {
        $student = $this->studentUser();
        $student->forceFill([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ])->save();
        $routine = $this->createRoutine([
            'title' => 'Recuperação Muscular',
            'category' => 'Recuperação',
        ]);

        Sanctum::actingAs($student);

        // Toggle ON
        $response = $this->postJson("/api/v1/student/active-rest/{$routine->id}/favorite")
            ->assertOk()
            ->assertJsonPath('data.is_favorite', true);

        $this->assertDatabaseHas('active_rest_favorites', [
            'user_id' => $student->id,
            'active_rest_routine_id' => $routine->id,
        ]);

        // Toggle OFF
        $response = $this->postJson("/api/v1/student/active-rest/{$routine->id}/favorite")
            ->assertOk()
            ->assertJsonPath('data.is_favorite', false);

        $this->assertDatabaseMissing('active_rest_favorites', [
            'user_id' => $student->id,
            'active_rest_routine_id' => $routine->id,
        ]);
    }

    public function test_student_can_log_session_completion(): void
    {
        $student = $this->studentUser();
        $student->forceFill([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ])->save();
        $routine = $this->createRoutine([
            'title' => 'Recuperação Muscular',
            'category' => 'Recuperação',
        ]);

        Sanctum::actingAs($student);

        $this->postJson("/api/v1/student/active-rest/{$routine->id}/log", [
            'duration_spent' => 900, // 15 mins in seconds
            'feedback_score' => 4,
        ])->assertOk();

        $this->assertDatabaseHas('active_rest_logs', [
            'user_id' => $student->id,
            'active_rest_routine_id' => $routine->id,
            'duration_spent' => 900,
            'feedback_score' => 4,
        ]);
    }
}
