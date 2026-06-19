<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1ErrorEnvelopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_me_returns_standard_error_envelope(): void
    {
        $this->getJson('/api/v1/me')
            ->assertUnauthorized()
            ->assertJsonStructure(['error' => ['message', 'code']])
            ->assertJsonPath('error.code', 'unauthenticated');
    }

    public function test_validation_error_returns_standard_envelope(): void
    {
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
        $user = User::factory()->create();
        $user->assignRole('aluno');
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/nutrition/diary', [])
            ->assertStatus(422)
            ->assertJsonStructure(['error' => ['message', 'code', 'errors']])
            ->assertJsonPath('error.code', 'validation_error');
    }

    public function test_not_found_returns_standard_envelope(): void
    {
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
        $user = User::factory()->create();
        $user->assignRole('aluno');
        Sanctum::actingAs($user);

        $this->deleteJson('/api/v1/nutrition/diary/999999')
            ->assertNotFound()
            ->assertJsonStructure(['error' => ['message', 'code']])
            ->assertJsonPath('error.code', 'not_found');
    }
}
