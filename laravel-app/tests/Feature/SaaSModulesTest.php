<?php

namespace Tests\Feature;

use App\Models\PainRecord;
use App\Models\User;
use App\Services\BioimpedanceImportService;
use App\Services\DigitalPrescriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class SaaSModulesTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_pain_records_can_be_stored_and_listed(): void
    {
        $user = $this->userWithRole('aluno');
        $this->actingAs($user);

        $response = $this->post(route('pain-mapping.store'), [
            'pain_points' => ['Lombar', 'Ombro Esquerdo'],
            'eva_level' => 6,
            'notes' => 'Dor ao realizar agachamento.',
            'assessment_date' => now()->toDateTimeString(),
        ]);

        $response->assertRedirect(route('pain-mapping.index'));
        $this->assertDatabaseHas('pain_records', [
            'user_id' => $user->id,
            'eva_level' => 6,
            'notes' => 'Dor ao realizar agachamento.',
        ]);

        $getResponse = $this->get(route('pain-mapping.index'));
        $getResponse->assertStatus(200);
        $getResponse->assertSee('Lombar');
    }

    public function test_bioimpedance_import_service_parses_heuristics(): void
    {
        $user = $this->userWithRole('paciente');
        $importer = new BioimpedanceImportService();

        $rawText = "InBody Report\nWeight: 82.5\nBF%: 14.8\nMuscle %: 44.2\nPhase Angle: 6.8";
        $assessment = $importer->parseAndCreate([
            'file_content' => $rawText,
            'file_type' => 'txt',
            'assessment_date' => now()->toDateString(),
        ], $user->id);

        $this->assertEquals(82.5, $assessment->weight_kg);
        $this->assertEquals(14.8, $assessment->bf_percent);
        $this->assertEquals(44.2, $assessment->muscle_percent);
        $this->assertEquals(6.8, $assessment->phase_angle);
    }

    public function test_digital_prescription_service_generates_memed_and_icp_signatures(): void
    {
        $patient = $this->userWithRole('paciente');
        $doctor = $this->userWithRole('professional');
        
        $prescriptionService = new DigitalPrescriptionService();

        $prescription = $prescriptionService->createMemedPrescription($patient, $doctor, [
            ['nome' => 'Dipirona 500mg', 'posologia' => '1 comprimido de 6 em 6 horas']
        ]);

        $this->assertEquals('success', $prescription['status']);
        $this->assertStringContainsString('memed_', $prescription['memed_prescription_id']);
    }
}
