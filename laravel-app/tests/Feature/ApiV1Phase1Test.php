<?php

namespace Tests\Feature;

use App\Models\DeviceToken;
use App\Models\FoodEntry;
use App\Models\Role;
use App\Models\User;
use App\Services\StudentRoleBridgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1Phase1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
        Role::firstOrCreate(['name' => 'paciente'], ['label' => 'Paciente']);
    }

    public function test_auth_token_bridges_aluno_to_paciente_role(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('secret123'),
            'status' => 'active',
        ]);
        $user->assignRole('aluno');

        $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'android-test',
        ])->assertOk();

        $this->assertTrue($user->fresh()->hasRole('paciente'));
    }

    public function test_auth_refresh_issues_new_token(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/auth/refresh', ['device_name' => 'android-refresh'])
            ->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_at']);
    }

    public function test_device_registration(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/devices', [
            'token' => 'fcm-token-abc123',
            'platform' => 'android',
            'device_name' => 'pixel-test',
        ])
            ->assertCreated()
            ->assertJsonPath('data.registered', true);

        $this->assertDatabaseHas('device_tokens', [
            'user_id' => $user->id,
            'token' => 'fcm-token-abc123',
            'is_active' => true,
        ]);
    }

    public function test_nutrition_diary_crud(): void
    {
        $user = User::factory()->create();
        $user->assignRole('aluno');
        Sanctum::actingAs($user);

        $create = $this->postJson('/api/v1/nutrition/diary', [
            'entry_date' => now()->toDateString(),
            'food_name' => 'Banana',
            'calories' => 90,
            'protein_g' => 1,
            'carbs_g' => 23,
            'fat_g' => 0.3,
            'meal_type' => 'snack',
        ])->assertCreated();

        $entryId = $create->json('data.id');

        $this->putJson("/api/v1/nutrition/diary/{$entryId}", [
            'food_name' => 'Banana prata',
            'calories' => 95,
            'meal_type' => 'snack',
        ])->assertOk()
            ->assertJsonPath('data.food_name', 'Banana prata');

        $this->deleteJson("/api/v1/nutrition/diary/{$entryId}")
            ->assertOk()
            ->assertJsonPath('data.deleted', true);

        $this->assertDatabaseMissing('food_entries', ['id' => $entryId]);
    }

    public function test_exercise_log_sync(): void
    {
        $user = User::factory()->create();
        $user->assignRole('aluno');
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/exercise-logs/sync', [
            'entry_date' => now()->toDateString(),
            'activity_type' => 'Corrida',
            'duration_min' => 30,
            'calories_burned' => 250,
        ])
            ->assertCreated()
            ->assertJsonPath('data.synced', true);
    }

    public function test_assessment_store(): void
    {
        $user = User::factory()->create();
        $user->assignRole('aluno');
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/assessments', [
            'assessment_date' => now()->toDateString(),
            'weight_kg' => 80,
            'bf_percent' => 18,
        ])
            ->assertCreated()
            ->assertJsonPath('data.weight_kg', 80);
    }

    public function test_student_role_bridge_service(): void
    {
        $user = User::factory()->create();
        $user->assignRole('aluno');

        $bridged = app(StudentRoleBridgeService::class)->ensurePortalAccess($user);

        $this->assertTrue($bridged);
        $this->assertTrue($user->fresh()->hasRole('paciente'));
    }

    public function test_profile_includes_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('aluno');
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonStructure(['data' => ['roles', 'is_student']]);
    }
}
