<?php

namespace Tests\Feature;

use App\Models\HealthAlert;
use App\Models\Profession;
use App\Models\ProfessionalAppointment;
use App\Models\ProfessionalProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1Sprint3Test extends TestCase
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
        $user = User::factory()->create(['status' => 'active']);
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

    public function test_professional_lists_linked_patients(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $professional->patients()->attach($patient->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/professional/patients')
            ->assertOk()
            ->assertJsonPath('data.patients.0.id', $patient->id)
            ->assertJsonPath('data.patients.0.name', $patient->name)
            ->assertJsonPath('data.patients.0.status', 'Ativo');
    }

    public function test_professional_dashboard_returns_stats(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $professional->patients()->attach($patient->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        ProfessionalAppointment::create([
            'professional_id' => $professional->id,
            'patient_id' => $patient->id,
            'appointment_at' => now()->setTime(14, 0),
            'service_type' => 'Consulta',
            'status' => ProfessionalAppointment::STATUS_SCHEDULED,
        ]);

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/professional/dashboard')
            ->assertOk()
            ->assertJsonPath('data.stats.total_patients', 1)
            ->assertJsonPath('data.stats.today_appointments', 1);
    }

    public function test_professional_lists_and_updates_appointments(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();

        $appointment = ProfessionalAppointment::create([
            'professional_id' => $professional->id,
            'patient_id' => $patient->id,
            'appointment_at' => now()->addDay()->setTime(9, 0),
            'service_type' => 'Avaliação',
            'status' => ProfessionalAppointment::STATUS_SCHEDULED,
        ]);

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/professional/appointments')
            ->assertOk()
            ->assertJsonCount(1, 'data.appointments');

        $this->patchJson('/api/v1/professional/appointments/'.$appointment->id.'/status', [
            'status' => ProfessionalAppointment::STATUS_CONFIRMED,
        ])
            ->assertOk()
            ->assertJsonPath('data.status', ProfessionalAppointment::STATUS_CONFIRMED);
    }

    public function test_professional_lists_alerts_for_patients(): void
    {
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $professional->patients()->attach($patient->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        HealthAlert::create([
            'user_id' => $patient->id,
            'type' => 'nutrition',
            'severity' => 'warning',
            'message' => 'Baixa adesão alimentar',
            'is_read' => false,
        ]);

        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/professional/alerts?unread_only=1')
            ->assertOk()
            ->assertJsonCount(1, 'data.alerts')
            ->assertJsonPath('data.alerts.0.patient_id', $patient->id);
    }

    public function test_student_cannot_access_professional_dashboard(): void
    {
        $student = User::factory()->create(['status' => 'active']);
        $student->assignRole('aluno');
        Sanctum::actingAs($student);

        $this->getJson('/api/v1/professional/dashboard')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'forbidden');
    }
}
