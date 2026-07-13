<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\MedicalReport;
use App\Models\MedicalPrescription;
use App\Models\MedicalCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1StudentMedicalDocumentsTest extends TestCase
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
        $user->profile()->create([
            'birth_date' => '1995-05-15',
            'sex' => 'M',
        ]);

        $permission = Permission::firstOrCreate(
            ['name' => 'portal.access'],
            ['label' => 'Acesso ao Portal Usuário']
        );
        $user->permissions()->syncWithoutDetaching([$permission->id]);

        return $user;
    }

    public function test_medical_documents_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/student/medical-documents')->assertUnauthorized();
    }

    public function test_student_can_list_own_medical_documents(): void
    {
        $student = $this->studentUser();
        $professional = User::factory()->create();

        $report = MedicalReport::create([
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'title' => 'Laudo Teste API',
            'date' => now(),
            'conclusion' => 'Paciente saudável',
        ]);

        $prescription = MedicalPrescription::create([
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'date' => now(),
            'objective' => 'Objetivo Receita',
            'medicine' => 'Medicamento A',
        ]);

        $certificate = MedicalCertificate::create([
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'date' => now(),
            'reason' => 'Aptidão',
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'observations' => 'Nenhuma observação',
        ]);

        Sanctum::actingAs($student);

        $response = $this->getJson('/api/v1/student/medical-documents')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'reports',
                    'prescriptions',
                    'certificates'
                ]
            ]);

        $response->assertJsonPath('data.reports.0.title', 'Laudo Teste API');
        $response->assertJsonPath('data.prescriptions.0.title', 'Objetivo Receita');
        $response->assertJsonPath('data.certificates.0.title', 'Aptidão');
    }

    public function test_student_can_download_own_report(): void
    {
        $student = $this->studentUser();
        $professional = User::factory()->create();

        $report = MedicalReport::create([
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'title' => 'Laudo Teste API',
            'date' => now(),
            'conclusion' => 'Paciente saudável',
        ]);

        Sanctum::actingAs($student);

        $response = $this->get('/api/v1/student/medical-documents/reports/' . $report->id . '/download');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_student_cannot_download_others_report(): void
    {
        $student = $this->studentUser();
        $otherStudent = $this->studentUser();
        $professional = User::factory()->create();

        $report = MedicalReport::create([
            'patient_id' => $otherStudent->id,
            'professional_id' => $professional->id,
            'title' => 'Laudo Alheio',
            'date' => now(),
            'conclusion' => 'Paciente saudável',
        ]);

        Sanctum::actingAs($student);

        $this->get('/api/v1/student/medical-documents/reports/' . $report->id . '/download')
            ->assertStatus(403);
    }
}
