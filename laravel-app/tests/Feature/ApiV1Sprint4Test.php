<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\ClinicProtocol;
use App\Models\Especialidade;
use App\Models\HealthAlert;
use App\Models\Profession;
use App\Models\ProfessionalProfile;
use App\Models\Role;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1Sprint4Test extends TestCase
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

    private function link(User $professional, User $patient): void
    {
        $professional->patients()->attach($patient->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);
    }

    public function test_professional_lists_patient_training_plans(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $this->link($professional, $patient);

        TrainingPlan::create([
            'user_id' => $patient->id,
            'professional_id' => $professional->id,
            'creator_id' => $professional->id,
            'name' => 'Treino A',
            'status' => 'Ativo',
            'is_active' => true,
        ]);

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/professional/patients/'.$patient->id.'/training-plans')
            ->assertOk()
            ->assertJsonCount(1, 'data.plans')
            ->assertJsonPath('data.plans.0.name', 'Treino A');
    }

    public function test_professional_creates_quick_training_plan(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $this->link($professional, $patient);

        Sanctum::actingAs($professional);

        $this->postJson('/api/v1/professional/patients/'.$patient->id.'/training-plans', [
            'name' => 'Plano Mobile',
            'goal' => 'Emagrecimento',
            'description' => 'Prescrição rápida via app',
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Plano Mobile');

        $this->assertDatabaseHas('training_plans', [
            'user_id' => $patient->id,
            'professional_id' => $professional->id,
            'name' => 'Plano Mobile',
        ]);
    }

    private function createProtocol(User $professional, array $overrides = []): ClinicProtocol
    {
        $specialty = Especialidade::firstOrCreate(
            ['codigo' => 'TEST-ESP'],
            [
                'nome' => 'Nutrição Esportiva',
                'categoria' => 'saude',
                'status' => 'Ativo',
                'profession_id' => $professional->professionalProfile->profession_id,
            ]
        );

        return ClinicProtocol::create(array_merge([
            'academy_company_id' => $professional->academy_company_id,
            'especialidade_id' => $specialty->id,
            'type' => 'training',
            'name' => 'Protocolo Base',
            'description' => 'Desc',
            'objective' => 'Força',
            'protocol' => 'Conteúdo',
            'frequency' => 3,
            'duration' => 45,
        ], $overrides));
    }

    public function test_professional_applies_protocol_to_patient(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $this->link($professional, $patient);

        $protocol = $this->createProtocol($professional);

        Sanctum::actingAs($professional);

        $this->postJson('/api/v1/professional/patients/'.$patient->id.'/training-plans', [
            'name' => 'ignored',
            'protocol_id' => $protocol->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Protocolo Base');
    }

    public function test_professional_lists_and_creates_patient_assessments(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $this->link($professional, $patient);

        Sanctum::actingAs($professional);

        $this->postJson('/api/v1/professional/patients/'.$patient->id.'/assessments', [
            'assessment_date' => now()->toDateString(),
            'weight_kg' => 80.5,
            'bf_percent' => 18.2,
            'notes' => 'Avaliação presencial',
        ])
            ->assertCreated()
            ->assertJsonPath('data.weight_kg', 80.5);

        $this->getJson('/api/v1/professional/patients/'.$patient->id.'/assessments')
            ->assertOk()
            ->assertJsonCount(1, 'data.assessments');
    }

    public function test_professional_marks_alert_as_read(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $this->link($professional, $patient);

        $alert = HealthAlert::create([
            'user_id' => $patient->id,
            'type' => 'training',
            'severity' => 'warning',
            'message' => 'Treino expirado',
            'is_read' => false,
        ]);

        Sanctum::actingAs($professional);

        $this->patchJson('/api/v1/professional/alerts/'.$alert->id.'/read')
            ->assertOk()
            ->assertJsonPath('data.is_read', true);

        $this->assertEquals(1, $alert->fresh()->is_read);
    }

    public function test_professional_lists_protocols(): void
    {
        $professional = $this->professionalUser();
        $this->createProtocol($professional, ['name' => 'P1', 'description' => 'D', 'objective' => 'O', 'protocol' => 'X']);

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/professional/protocols?type=training')
            ->assertOk()
            ->assertJsonCount(1, 'data.protocols');
    }
}
