<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\DeviceToken;
use App\Models\HealthAlert;
use App\Models\EvolutionPhoto;
use App\Models\Profession;
use App\Models\ProfessionalProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1Sprint5Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
        Role::firstOrCreate(['name' => 'paciente'], ['label' => 'Paciente']);
        Role::firstOrCreate(['name' => 'professional'], ['label' => 'Profissional']);
    }

    private function professionalUser(): User
    {
        $company = AcademyCompany::create([
            'name' => 'Clínica Teste',
            'slug' => 'clinica-teste',
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);
        $user = User::factory()->create(['status' => 'active', 'academy_company_id' => $company->id]);
        $user->assignRole('professional');
        $profession = Profession::firstOrCreate(
            ['slug' => 'personal-trainer'],
            ['name' => 'Personal Trainer']
        );
        ProfessionalProfile::create([
            'user_id' => $user->id,
            'profession_id' => $profession->id,
            'registration_number' => 'TEST-123',
            'council' => 'CREF',
            'registration_uf' => 'SP',
            'registration_expiry_date' => now()->addYear()->toDateString(),
            'appointment_duration' => 60,
            'appointment_interval' => 0,
            'is_public' => true,
        ]);

        return $user;
    }

    private function patientUser(): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('paciente');

        return $user;
    }

    public function test_professional_lists_patient_evolution_photos(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $professional->patients()->attach($patient->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        EvolutionPhoto::create([
            'user_id' => $patient->id,
            'photo_path' => 'evolution/test.jpg',
            'type' => 'front',
            'registered_date' => now()->toDateString(),
        ]);

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/professional/patients/'.$patient->id.'/evolution-photos')
            ->assertOk()
            ->assertJsonCount(1, 'data.photos')
            ->assertJsonPath('data.photos.0.type', 'front');
    }

    public function test_health_alert_triggers_fcm_to_linked_professional(): void
    {
        config(['projeto.fcm_server_key' => 'test-fcm-key']);
        Http::fake([
            'fcm.googleapis.com/*' => Http::response(['success' => 1, 'failure' => 0, 'results' => [[]]]),
        ]);

        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $professional->patients()->attach($patient->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        DeviceToken::create([
            'user_id' => $professional->id,
            'token' => 'fcm-device-token',
            'platform' => 'android',
            'is_active' => true,
        ]);

        HealthAlert::create([
            'user_id' => $patient->id,
            'type' => 'training',
            'severity' => 'warning',
            'message' => 'Treino vencido',
            'is_read' => false,
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fcm.googleapis.com')
                && str_contains($request->body(), 'Treino vencido');
        });
    }

    public function test_auth_refresh_issues_new_token(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('aluno');
        $pat = $user->createToken('test-device');
        $oldTokenId = $pat->accessToken->id;

        $this->withToken($pat->plainTextToken)
            ->postJson('/api/v1/auth/refresh')
            ->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'user']);

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $oldTokenId]);
        $this->assertEquals(1, $user->fresh()->tokens()->count());
    }
}
