<?php

namespace Tests\Unit;

use App\Models\Clinic;
use App\Models\HealthPermission;
use App\Models\Profession;
use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Services\MedicalRecordModuleManager;
use Tests\TestCase;

class MedicalRecordModuleManagerTest extends TestCase
{
    public function test_medical_professional_receives_clinical_modules(): void
    {
        $professional = $this->professional('Medico');
        $patient = new User(['name' => 'Paciente Teste']);
        $patient->setRelation('clinic', null);

        $modules = collect(app(MedicalRecordModuleManager::class)->forProfessional($professional, $patient));

        $this->assertTrue($modules->contains('key', 'summary'));
        $this->assertTrue($modules->contains('key', 'prescriptions'));
        $this->assertTrue($modules->contains('key', 'reports'));
        $this->assertTrue($modules->contains('key', 'certificates'));
    }

    public function test_psychologist_receives_restricted_modules(): void
    {
        $professional = $this->professional('Psicologo');
        $patient = new User(['name' => 'Paciente Teste']);
        $patient->setRelation('clinic', null);

        $modules = collect(app(MedicalRecordModuleManager::class)->forProfessional($professional, $patient));

        $this->assertSame('restricted', $modules->firstWhere('key', 'session_notes')['status']);
        $this->assertFalse($modules->firstWhere('key', 'session_notes')['can_access']);
        $this->assertNull($modules->firstWhere('key', 'session_notes')['route']);
        $this->assertSame('restricted', $modules->firstWhere('key', 'restricted_notes')['status']);

        $navigation = collect(app(MedicalRecordModuleManager::class)->navigationForProfessional($professional, $patient));

        $this->assertFalse($navigation->contains('label', 'Registros de sessão'));
    }

    public function test_restricted_module_is_accessible_with_health_permission(): void
    {
        $professional = $this->professional('Psicologo');
        $professional->forceFill(['id' => 10]);

        $patient = new User(['name' => 'Paciente Teste']);
        $patient->forceFill(['id' => 20]);
        $patient->setRelation('clinic', null);
        $patient->setRelation('healthPermissions', new Collection([
            new HealthPermission([
                'patient_id' => 20,
                'professional_id' => 10,
                'data_type' => 'psychology_session_notes',
                'access_level' => 'view',
            ]),
        ]));

        $modules = collect(app(MedicalRecordModuleManager::class)->forProfessional($professional, $patient));
        $sessionNotes = $modules->firstWhere('key', 'session_notes');

        $this->assertTrue($sessionNotes['can_access']);
        $this->assertSame('professional.patients.medical-records.session-notes.index', $sessionNotes['route']);

        $navigation = collect(app(MedicalRecordModuleManager::class)->navigationForProfessional($professional, $patient));

        $this->assertTrue($navigation->contains('label', 'Registros de sessão'));
        $this->assertSame(
            'professional.patients.medical-records.session-notes.index',
            $navigation->firstWhere('label', 'Registros de sessão')['route']
        );
    }

    public function test_clinic_enabled_modules_filter_optional_modules(): void
    {
        $clinic = new Clinic([
            'name' => 'Clinica Teste',
            'slug' => 'clinica-teste',
            'enabled_modules' => ['clinical_docs'],
        ]);

        $professional = $this->professional('Personal Trainer');
        $professional->setRelation('clinic', $clinic);

        $patient = new User(['name' => 'Paciente Teste']);
        $patient->setRelation('clinic', $clinic);

        $modules = collect(app(MedicalRecordModuleManager::class)->forProfessional($professional, $patient));

        $this->assertTrue($modules->contains('key', 'summary'));
        $this->assertTrue($modules->contains('key', 'documents'));
        $this->assertFalse($modules->contains('key', 'trainings'));
    }

    public function test_personal_trainer_receives_active_assessments_and_photos(): void
    {
        $professional = $this->professional('Personal Trainer');
        $patient = new User(['name' => 'Paciente Teste']);
        $patient->setRelation('clinic', null);

        $modules = collect(app(MedicalRecordModuleManager::class)->forProfessional($professional, $patient));

        $this->assertSame('active', $modules->firstWhere('key', 'assessments')['status']);
        $this->assertSame('professional.patients.medical-records.assessments.index', $modules->firstWhere('key', 'assessments')['route']);
        $this->assertSame('active', $modules->firstWhere('key', 'photos')['status']);
        $this->assertSame('professional.patients.medical-records.photos.index', $modules->firstWhere('key', 'photos')['route']);
    }

    public function test_fisioterapeuta_receives_active_pain_and_protocol_modules(): void
    {
        $professional = $this->professional('Fisioterapeuta');
        $patient = new User(['name' => 'Paciente Teste']);
        $patient->setRelation('clinic', null);

        $modules = collect(app(MedicalRecordModuleManager::class)->forProfessional($professional, $patient));

        $this->assertSame('active', $modules->firstWhere('key', 'pain_tracking')['status']);
        $this->assertSame('professional.patients.medical-records.pain.index', $modules->firstWhere('key', 'pain_tracking')['route']);
        $this->assertSame('active', $modules->firstWhere('key', 'therapeutic_exercises')['status']);
        $this->assertSame('professional.patients.medical-records.protocols.index', $modules->firstWhere('key', 'therapeutic_exercises')['route']);
    }

    public function test_esteticista_receives_active_aesthetic_protocols(): void
    {
        $professional = $this->professional('Esteticista');
        $patient = new User(['name' => 'Paciente Teste']);
        $patient->setRelation('clinic', null);

        $modules = collect(app(MedicalRecordModuleManager::class)->forProfessional($professional, $patient));

        $this->assertSame('active', $modules->firstWhere('key', 'aesthetic_protocols')['status']);
        $this->assertSame('professional.patients.medical-records.protocols.index', $modules->firstWhere('key', 'aesthetic_protocols')['route']);
    }

    public function test_nutricionista_receives_active_nutrition_module(): void
    {
        $professional = $this->professional('Nutricionista');
        $patient = new User(['name' => 'Paciente Teste']);
        $patient->setRelation('clinic', null);

        $modules = collect(app(MedicalRecordModuleManager::class)->forProfessional($professional, $patient));

        $this->assertSame('active', $modules->firstWhere('key', 'nutrition')['status']);
        $this->assertSame('professional.patients.medical-records.nutrition.index', $modules->firstWhere('key', 'nutrition')['route']);
    }

    public function test_clinic_module_options_include_professional_record_modules(): void
    {
        $options = app(MedicalRecordModuleManager::class)->clinicModuleOptions();

        $this->assertArrayHasKey('aesthetic_protocols', $options);
        $this->assertArrayHasKey('therapeutic_exercises', $options);
        $this->assertArrayHasKey('pain_tracking', $options);
        $this->assertSame('active', $options['aesthetic_protocols']['status']);
    }

    /**
     */
    private function professional(string $professionName): User
    {
        $profession = new Profession([
            'name' => $professionName,
            'slug' => str($professionName)->slug()->toString(),
        ]);

        $profile = new ProfessionalProfile([
            'specialty' => $professionName,
        ]);
        $profile->setRelation('profession', $profession);
        $profile->setRelation('especialidade', null);

        $user = new User(['name' => 'Profissional Teste']);
        $user->setRelation('professionalProfile', $profile);
        $user->setRelation('clinic', null);

        return $user;
    }
}
