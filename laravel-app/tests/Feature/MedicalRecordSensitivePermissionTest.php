<?php

namespace Tests\Feature;

use App\Models\AdminLog;
use App\Models\BodyAssessment;
use App\Models\EvolutionPhoto;
use App\Models\HealthPermission;
use App\Models\MealTemplate;
use App\Models\MealTemplateItem;
use App\Models\MedicalCertificate;
use App\Models\MedicalPrescription;
use App\Models\MedicalReport;
use App\Models\Profession;
use App\Models\ProfessionalPatient;
use App\Models\ProfessionalProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class MedicalRecordSensitivePermissionTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_patient_can_grant_sensitive_health_permission_to_professional(): void
    {
        [$professional, $patient, $link] = $this->linkedPsychologistAndPatient();

        $this->actingAs($patient)
            ->withSession([
                'active_role' => 'paciente',
                'active_professional_id' => $professional->id,
            ])
            ->post(route('patient.my-professionals.update-permissions', $link), [
                'permissions' => [
                    'view_medical_records' => '1',
                    'psychology_session_notes' => '1',
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('health_permissions', [
            'patient_id' => $patient->id,
            'professional_id' => $professional->id,
            'data_type' => 'psychology_session_notes',
            'access_level' => 'view',
            'is_confidential' => true,
        ]);

        $this->assertDatabaseHas('admin_logs', [
            'user_id' => $patient->id,
            'action' => 'PATIENT_UPDATED_PERMISSIONS',
        ]);
    }

    public function test_patient_can_revoke_professional_link_and_it_is_audited(): void
    {
        [$professional, $patient, $link] = $this->linkedPsychologistAndPatient();

        $this->actingAs($patient)
            ->withSession([
                'active_role' => 'paciente',
                'active_professional_id' => $professional->id,
            ])
            ->post(route('patient.my-professionals.revoke', $link))
            ->assertRedirect();

        $this->assertDatabaseHas('pacientes', [
            'id' => $link->id,
            'status' => 'Não',
        ]);

        $this->assertDatabaseHas('admin_logs', [
            'user_id' => $patient->id,
            'action' => 'PATIENT_REVOKED_PROFESSIONAL_LINK',
        ]);
    }

    public function test_professional_cannot_access_restricted_evolutions_without_permission(): void
    {
        [$professional, $patient] = $this->linkedPsychologistAndPatient();

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->get(route('professional.patients.medical-records.evolutions.index', $patient))
            ->assertForbidden();
    }

    public function test_professional_access_to_restricted_evolutions_is_audited_when_allowed(): void
    {
        [$professional, $patient] = $this->linkedPsychologistAndPatient();

        HealthPermission::create([
            'patient_id' => $patient->id,
            'professional_id' => $professional->id,
            'data_type' => 'psychology_session_notes',
            'access_level' => 'view',
            'is_confidential' => true,
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->get(route('professional.patients.medical-records.evolutions.index', $patient))
            ->assertOk();

        $this->assertDatabaseHas('admin_logs', [
            'user_id' => $professional->id,
            'action' => 'ACCESS_VIEW_RESTRICTED_MEDICAL_RECORD',
        ]);

        $log = AdminLog::where('user_id', $professional->id)
            ->where('action', 'ACCESS_VIEW_RESTRICTED_MEDICAL_RECORD')
            ->latest('created_at')
            ->first();

        $this->assertSame($patient->id, $log->payload['patient_id']);
        $this->assertSame('session_notes', $log->payload['module_key']);
        $this->assertSame('psychology_session_notes', $log->payload['data_type']);
    }

    public function test_psychologist_can_use_dedicated_session_notes_when_allowed(): void
    {
        [$professional, $patient] = $this->linkedPsychologistAndPatient();

        HealthPermission::create([
            'patient_id' => $patient->id,
            'professional_id' => $professional->id,
            'data_type' => 'psychology_session_notes',
            'access_level' => 'view',
            'is_confidential' => true,
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->get(route('professional.patients.medical-records.session-notes.index', $patient))
            ->assertOk();

        $this->post(route('professional.patients.medical-records.session-notes.store', $patient), [
            'date' => now()->format('Y-m-d H:i:s'),
            'chief_complaint' => 'Demanda inicial',
            'assessment' => 'Registro restrito',
            'conduct' => 'Acompanhar',
            'observations' => 'Interno',
        ])->assertRedirect();

        $this->assertDatabaseHas('medical_evolutions', [
            'patient_id' => $patient->id,
            'professional_id' => $professional->id,
            'type' => 'Registro de sessao',
            'assessment' => 'Registro restrito',
        ]);
    }

    public function test_professional_can_remove_own_assessment_from_linked_patient_record(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Personal Trainer');

        $assessment = BodyAssessment::create([
            'user_id' => $patient->id,
            'professional_id' => $professional->id,
            'assessment_date' => now()->toDateString(),
            'weight_kg' => 80,
            'status' => 'approved',
            'created_by' => 'professional',
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->delete(route('professional.patients.medical-records.assessments.destroy', [$patient, $assessment]))
            ->assertRedirect();

        $this->assertDatabaseMissing('body_assessments', ['id' => $assessment->id]);
        $this->assertDatabaseHas('medical_histories', [
            'patient_id' => $patient->id,
            'user_id' => $professional->id,
            'action_type' => 'delete',
            'module' => 'assessment',
        ]);
    }

    public function test_professional_can_update_own_assessment(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Personal Trainer');

        $assessment = BodyAssessment::create([
            'user_id' => $patient->id,
            'professional_id' => $professional->id,
            'assessment_date' => now()->toDateString(),
            'weight_kg' => 80,
            'status' => 'approved',
            'created_by' => 'professional',
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->put(route('professional.patients.medical-records.assessments.update', [$patient, $assessment]), [
                'assessment_date' => now()->toDateString(),
                'weight_kg' => 82,
                'bf_percent' => 18,
                'muscle_percent' => 40,
                'waist' => 84,
                'chest' => 100,
                'hips' => 98,
                'blood_pressure' => '120/80',
                'heart_rate' => 70,
                'notes' => 'Atualizado no teste',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('body_assessments', [
            'id' => $assessment->id,
            'weight_kg' => 82,
            'notes' => 'Atualizado no teste',
        ]);
    }

    public function test_professional_cannot_remove_assessment_from_other_professional(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Personal Trainer');
        $otherProfessional = $this->userWithRole('professional', ['status' => 'active']);

        $assessment = BodyAssessment::create([
            'user_id' => $patient->id,
            'professional_id' => $otherProfessional->id,
            'assessment_date' => now()->toDateString(),
            'weight_kg' => 80,
            'status' => 'approved',
            'created_by' => 'professional',
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->delete(route('professional.patients.medical-records.assessments.destroy', [$patient, $assessment]))
            ->assertNotFound();

        $this->assertDatabaseHas('body_assessments', ['id' => $assessment->id]);
    }

    public function test_professional_can_remove_own_meal_template_and_items(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Nutricionista');

        $template = MealTemplate::create([
            'user_id' => $patient->id,
            'professional_id' => $professional->id,
            'name' => 'Dia base',
        ]);

        $item = MealTemplateItem::create([
            'meal_template_id' => $template->id,
            'meal_type' => 'breakfast',
            'food_name' => 'Ovos',
            'calories' => 200,
            'protein_g' => 20,
            'carbs_g' => 2,
            'fat_g' => 12,
            'position' => 0,
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->delete(route('professional.patients.medical-records.nutrition.meal-template.destroy', [$patient, $template]))
            ->assertRedirect();

        $this->assertDatabaseMissing('meal_templates', ['id' => $template->id]);
        $this->assertDatabaseMissing('meal_template_items', ['id' => $item->id]);
    }

    public function test_professional_can_update_own_pain_record(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Fisioterapeuta');

        $record = \App\Models\PainRecord::create([
            'user_id' => $patient->id,
            'professional_id' => $professional->id,
            'pain_points' => ['region' => 'Joelho', 'laterality' => 'Direito'],
            'eva_level' => 7,
            'notes' => 'Inicial',
            'assessment_date' => now(),
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->put(route('professional.patients.medical-records.pain.update', [$patient, $record]), [
                'assessment_date' => now()->format('Y-m-d H:i:s'),
                'eva_level' => 4,
                'region' => 'Lombar',
                'laterality' => 'Central',
                'notes' => 'Melhorou',
            ])
            ->assertRedirect();

        $record->refresh();
        $this->assertSame(4, $record->eva_level);
        $this->assertSame('Lombar', $record->pain_points['region']);
        $this->assertSame('Melhorou', $record->notes);
    }

    public function test_professional_can_update_evolution_photo_metadata(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Personal Trainer');

        $photo = EvolutionPhoto::create([
            'user_id' => $patient->id,
            'photo_path' => 'evolution/test.jpg',
            'type' => 'front',
            'registered_date' => now()->toDateString(),
            'weight_kg' => 80,
            'notes' => 'Inicial',
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->put(route('professional.patients.medical-records.photos.update', [$patient, $photo]), [
                'type' => 'side',
                'registered_date' => now()->toDateString(),
                'weight_kg' => 81,
                'notes' => 'Atualizada',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('evolution_photos', [
            'id' => $photo->id,
            'type' => 'side',
            'notes' => 'Atualizada',
        ]);
    }

    public function test_professional_can_update_own_meal_template_items(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Nutricionista');

        $template = MealTemplate::create([
            'user_id' => $patient->id,
            'professional_id' => $professional->id,
            'name' => 'Dia base',
        ]);

        MealTemplateItem::create([
            'meal_template_id' => $template->id,
            'meal_type' => 'breakfast',
            'food_name' => 'Ovos',
            'calories' => 200,
            'protein_g' => 20,
            'carbs_g' => 2,
            'fat_g' => 12,
            'position' => 0,
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->put(route('professional.patients.medical-records.nutrition.meal-template.update', [$patient, $template]), [
                'name' => 'Dia atualizado',
                'items' => [
                    [
                        'meal_type' => 'lunch',
                        'food_name' => 'Frango com arroz',
                        'calories' => 500,
                        'protein_g' => 35,
                        'carbs_g' => 60,
                        'fat_g' => 10,
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('meal_templates', [
            'id' => $template->id,
            'name' => 'Dia atualizado',
        ]);
        $this->assertDatabaseHas('meal_template_items', [
            'meal_template_id' => $template->id,
            'meal_type' => 'lunch',
            'food_name' => 'Frango com arroz',
        ]);
    }

    public function test_medical_professional_can_update_and_remove_report(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Medico');

        $report = MedicalReport::create([
            'patient_id' => $patient->id,
            'professional_id' => $professional->id,
            'title' => 'Laudo inicial',
            'date' => now(),
            'description' => 'Descricao',
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->put(route('professional.patients.medical-records.reports.update', [$patient, $report]), [
                'title' => 'Laudo atualizado',
                'date' => now()->toDateString(),
                'description' => 'Nova descricao',
                'conclusion' => 'Conclusao',
                'observations' => 'Obs',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('medical_reports', [
            'id' => $report->id,
            'title' => 'Laudo atualizado',
        ]);

        $this->delete(route('professional.patients.medical-records.reports.destroy', [$patient, $report]))
            ->assertRedirect();

        $this->assertDatabaseMissing('medical_reports', ['id' => $report->id]);
    }

    public function test_medical_professional_can_update_and_remove_prescription(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Medico');

        $prescription = MedicalPrescription::create([
            'patient_id' => $patient->id,
            'professional_id' => $professional->id,
            'date' => now(),
            'medicine' => 'Creatina',
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->put(route('professional.patients.medical-records.prescriptions.update', [$patient, $prescription]), [
                'medicine' => 'Vitamina D',
                'date' => now()->toDateString(),
                'dosage' => '1000 UI',
                'frequency' => '1x ao dia',
                'duration' => '30 dias',
                'observations' => 'Com refeicao',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('medical_prescriptions', [
            'id' => $prescription->id,
            'medicine' => 'Vitamina D',
        ]);

        $this->delete(route('professional.patients.medical-records.prescriptions.destroy', [$patient, $prescription]))
            ->assertRedirect();

        $this->assertDatabaseMissing('medical_prescriptions', ['id' => $prescription->id]);
    }

    public function test_medical_professional_can_update_and_remove_certificate(): void
    {
        [$professional, $patient] = $this->linkedProfessionalAndPatient('Medico');

        $certificate = MedicalCertificate::create([
            'patient_id' => $patient->id,
            'professional_id' => $professional->id,
            'date' => now(),
            'reason' => 'Afastamento',
            'start_date' => now(),
            'end_date' => now()->addDays(3),
            'period' => '3 dias',
        ]);

        $this->actingAs($professional)
            ->withSession(['active_role' => 'professional'])
            ->put(route('professional.patients.medical-records.certificates.update', [$patient, $certificate]), [
                'reason' => 'Aptidao',
                'date' => now()->toDateString(),
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDay()->toDateString(),
                'period' => '1 dia',
                'observations' => 'Liberado',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('medical_certificates', [
            'id' => $certificate->id,
            'reason' => 'Aptidao',
        ]);

        $this->delete(route('professional.patients.medical-records.certificates.destroy', [$patient, $certificate]))
            ->assertRedirect();

        $this->assertDatabaseMissing('medical_certificates', ['id' => $certificate->id]);
    }

    /**
     * @return array{0: User, 1: User, 2: ProfessionalPatient}
     */
    private function linkedPsychologistAndPatient(): array
    {
        return $this->linkedProfessionalAndPatient('Psicologo');
    }

    /**
     * @return array{0: User, 1: User, 2: ProfessionalPatient}
     */
    private function linkedProfessionalAndPatient(string $professionName): array
    {
        $professional = $this->userWithRole('professional', [
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $patient = $this->userWithRole('paciente', [
            'status' => 'active',
            'email_verified_at' => now(),
            'perfil_paciente_completo' => true,
        ]);

        UserProfile::create([
            'user_id' => $patient->id,
            'birth_date' => now()->subYears(30)->toDateString(),
        ]);

        $profession = Profession::firstOrCreate(
            ['name' => $professionName],
            ['slug' => str($professionName)->slug()->toString()]
        );

        ProfessionalProfile::create([
            'user_id' => $professional->id,
            'profession_id' => $profession->id,
            'specialty' => $professionName,
            'registration_number' => 'TEST-123',
            'council' => $professionName === 'Psicologo' ? 'CRP' : 'TEST',
            'registration_uf' => 'SP',
            'registration_expiry_date' => now()->addYear()->toDateString(),
        ]);

        $link = ProfessionalPatient::create([
            'profissional_id' => $professional->id,
            'user_id' => $patient->id,
            'status' => 'Sim',
            'data_cadastro' => now(),
        ]);

        return [$professional, $patient, $link];
    }
}
