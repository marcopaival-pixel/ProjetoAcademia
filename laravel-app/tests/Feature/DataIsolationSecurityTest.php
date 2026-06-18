<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\BodyAnalysis;
use App\Models\Clinic;
use App\Models\ProfessionalAppointment;
use App\Models\User;
use App\Policies\ProfessionalPatientPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class DataIsolationSecurityTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_admin_cannot_view_patient_without_impersonation(): void
    {
        $admin = $this->userWithRole('professional', ['is_admin' => true]);
        $patient = User::factory()->create();

        $policy = new ProfessionalPatientPolicy;

        $this->assertFalse($policy->view($admin, $patient));
        $this->assertFalse(Gate::forUser($admin)->allows('professionalPatient.view', $patient));
    }

    public function test_admin_can_view_patient_during_impersonation_in_same_tenant(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Empresa Teste',
            'slug' => 'empresa-teste',
        ]);

        $clinic = Clinic::create([
            'academy_company_id' => $company->id,
            'name' => 'Clínica Teste',
            'slug' => 'clinica-teste',
            'is_active' => true,
        ]);

        $admin = $this->userWithRole('professional', [
            'is_admin' => true,
            'academy_company_id' => $company->id,
            'clinic_id' => $clinic->id,
        ]);

        $patient = User::factory()->create([
            'academy_company_id' => $company->id,
            'clinic_id' => $clinic->id,
        ]);

        session([
            'impersonated_clinic_id' => $clinic->id,
            'impersonated_company_id' => $company->id,
        ]);

        $policy = new ProfessionalPatientPolicy;

        $this->assertTrue($policy->view($admin, $patient));
    }

    public function test_body_analysis_compare_blocks_other_users_records(): void
    {
        $userA = $this->userWithRole('aluno');
        $userB = $this->userWithRole('aluno');

        $analysisA = BodyAnalysis::create([
            'user_id' => $userA->id,
            'photo_path' => 'body-analyses/test-a.jpg',
            'view_type' => 'front',
        ]);

        $analysisB = BodyAnalysis::create([
            'user_id' => $userB->id,
            'photo_path' => 'body-analyses/test-b.jpg',
            'view_type' => 'side',
        ]);

        $this->actingAs($userA)
            ->withSession(['active_role' => 'aluno'])
            ->get(route('body-analysis.compare', ['id1' => $analysisB->id, 'id2' => $analysisA->id]))
            ->assertNotFound();
    }

    public function test_ai_job_status_rejects_foreign_job_key(): void
    {
        $owner = $this->userWithRole('aluno');
        $intruder = $this->userWithRole('aluno');

        $jobKey = 'ai_job_'.$owner->id.'_00000000-0000-0000-0000-000000000099';
        Cache::put($jobKey, ['status' => 'success', 'message' => 'segredo'], 600);

        $this->actingAs($intruder)
            ->withSession(['active_role' => 'aluno'])
            ->getJson(route('api.ai.orchestrator.status', ['jobKey' => $jobKey]))
            ->assertForbidden()
            ->assertJsonPath('status', 'error');
    }

    public function test_unrelated_user_cannot_cancel_appointment(): void
    {
        $professional = $this->userWithRole('professional');
        $patient = $this->userWithRole('paciente');
        $otherProfessional = $this->userWithRole('professional');

        $appointment = ProfessionalAppointment::create([
            'professional_id' => $professional->id,
            'patient_id' => $patient->id,
            'appointment_at' => now()->addDay(),
            'status' => ProfessionalAppointment::STATUS_SCHEDULED,
        ]);

        $this->actingAs($otherProfessional)
            ->withSession(['active_role' => 'professional'])
            ->postJson(route('agenda.cancel', $appointment))
            ->assertNotFound();
    }
}
