<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\StudentRoleBridgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1Sprint1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
        Role::firstOrCreate(['name' => 'paciente'], ['label' => 'Paciente']);
        Role::firstOrCreate(['name' => 'professional'], ['label' => 'Profissional']);
    }

    public function test_me_includes_mobile_profile_fields(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('aluno');
        app(StudentRoleBridgeService::class)->ensurePortalAccess($user->fresh());
        Sanctum::actingAs($user->fresh());

        $this->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.is_student', true)
            ->assertJsonPath('data.is_professional', false)
            ->assertJsonPath('data.active_patient_id', $user->id)
            ->assertJsonStructure([
                'data' => [
                    'panels',
                    'branding' => ['primary_color', 'accent_color', 'clinic_name'],
                ],
            ])
            ->assertJsonFragment(['panels' => ['student', 'patient']]);
    }

    public function test_professional_cannot_access_student_subscription_plans(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('professional');
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/subscriptions/plans')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'forbidden');
    }

    public function test_student_can_access_subscription_plans(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('aluno');
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/subscriptions/plans')
            ->assertOk()
            ->assertJsonStructure(['data' => ['plans']]);
    }

    public function test_professional_active_patient_header_requires_link(): void
    {
        $professional = User::factory()->create(['status' => 'active']);
        $professional->assignRole('professional');
        $patient = User::factory()->create(['status' => 'active']);
        $patient->assignRole('paciente');

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/me', [
            'X-Active-Patient-Id' => (string) $patient->id,
        ])
            ->assertForbidden()
            ->assertJsonPath('error.code', 'forbidden');
    }

    public function test_professional_active_patient_header_with_valid_link(): void
    {
        $professional = User::factory()->create(['status' => 'active']);
        $professional->assignRole('professional');
        $patient = User::factory()->create(['status' => 'active']);
        $patient->assignRole('paciente');

        $professional->patients()->attach($patient->id, ['status' => 'Sim']);

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/me', [
            'X-Active-Patient-Id' => (string) $patient->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.active_patient_id', $patient->id)
            ->assertJsonPath('data.is_professional', true);
    }
}
