<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_v1_health_is_public(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    public function test_api_v1_issues_token_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('ApiSecret123'),
            'status' => 'active',
        ]);

        $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'ApiSecret123',
            'device_name' => 'phpunit',
        ])
            ->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'user']);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_api_v1_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_api_v1_me_returns_profile_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }
}
