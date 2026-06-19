<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Profession;
use App\Models\ProfessionalAvailability;
use App\Models\ProfessionalAppointment;
use App\Models\ProfessionalProfile;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1Sprint2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
        Role::firstOrCreate(['name' => 'paciente'], ['label' => 'Paciente']);
        Role::firstOrCreate(['name' => 'professional'], ['label' => 'Profissional']);
    }

    private function studentUser(): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('aluno');
        $permission = Permission::firstOrCreate(
            ['name' => 'portal.access'],
            ['label' => 'Acesso ao Portal Usuário']
        );
        $user->permissions()->syncWithoutDetaching([$permission->id]);

        return $user;
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

    public function test_student_lists_linked_professionals(): void
    {
        $student = $this->studentUser();
        $professional = $this->professionalUser();
        $student->professionals()->attach($professional->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/student/professionals')
            ->assertOk()
            ->assertJsonPath('data.professionals.0.id', $professional->id)
            ->assertJsonPath('data.professionals.0.name', $professional->name);
    }

    public function test_student_lists_appointments(): void
    {
        $student = $this->studentUser();
        $professional = $this->professionalUser();

        ProfessionalAppointment::create([
            'professional_id' => $professional->id,
            'patient_id' => $student->id,
            'appointment_at' => now()->addDay()->setTime(10, 0),
            'service_type' => 'Avaliação',
            'status' => ProfessionalAppointment::STATUS_SCHEDULED,
        ]);

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/student/appointments')
            ->assertOk()
            ->assertJsonCount(1, 'data.appointments')
            ->assertJsonPath('data.appointments.0.service_type', 'Avaliação');
    }

    public function test_student_gets_available_slots(): void
    {
        $student = $this->studentUser();
        $professional = $this->professionalUser();
        $student->professionals()->attach($professional->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        $date = Carbon::now()->addWeekday()->startOfDay();
        if ($date->isWeekend()) {
            $date = $date->nextWeekday();
        }

        ProfessionalAvailability::create([
            'professional_id' => $professional->id,
            'day_of_week' => $date->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
        ]);

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/student/appointments/slots?professional_id='.$professional->id.'&date='.$date->toDateString())
            ->assertOk()
            ->assertJsonStructure(['data' => ['slots']]);
    }

    public function test_student_can_schedule_appointment(): void
    {
        $student = $this->studentUser();
        $professional = $this->professionalUser();
        $student->professionals()->attach($professional->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        $appointmentAt = Carbon::now()->addWeekday()->setTime(10, 0);
        if ($appointmentAt->isWeekend()) {
            $appointmentAt = $appointmentAt->nextWeekday()->setTime(10, 0);
        }

        ProfessionalAvailability::create([
            'professional_id' => $professional->id,
            'day_of_week' => $appointmentAt->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        Sanctum::actingAs($student);

        $this->postJson('/api/v1/student/appointments', [
            'professional_id' => $professional->id,
            'appointment_at' => $appointmentAt->toIso8601String(),
            'service_type' => 'Avaliação física',
            'notes' => 'Via app mobile',
        ])
            ->assertCreated()
            ->assertJsonPath('data.professional_id', $professional->id)
            ->assertJsonPath('data.service_type', 'Avaliação física');

        $this->assertDatabaseHas('professional_appointments', [
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
        ]);
    }

    public function test_professional_cannot_access_student_appointments(): void
    {
        $professional = $this->professionalUser();
        Sanctum::actingAs($professional);

        $this->getJson('/api/v1/student/appointments')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'forbidden');
    }
}
