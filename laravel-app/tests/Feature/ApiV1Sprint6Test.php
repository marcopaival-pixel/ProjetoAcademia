<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\AdminSetting;
use App\Models\EvolutionPhoto;
use App\Models\Plan;
use App\Models\Profession;
use App\Models\ProfessionalProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1Sprint6Test extends TestCase
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

    public function test_professional_uploads_patient_evolution_photo(): void
    {
        Storage::fake('local');
        $professional = $this->professionalUser();
        $patient = $this->patientUser();
        $professional->patients()->attach($patient->id, [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $professional->academy_company_id,
        ]);

        Sanctum::actingAs($professional);

        $file = UploadedFile::fake()->image('front.jpg');

        $this->postJson('/api/v1/professional/patients/'.$patient->id.'/evolution-photos', [
            'photo' => $file,
            'type' => 'front',
            'registered_date' => now()->toDateString(),
            'weight_kg' => 75.5,
        ])
            ->assertCreated()
            ->assertJsonPath('data.type', 'front');

        $this->assertEquals(1, EvolutionPhoto::withoutGlobalScopes()->where('user_id', $patient->id)->count());
    }

    public function test_checkout_free_plan_does_not_require_return_links(): void
    {
        AdminSetting::set('pagamento_ativo', 'false');

        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('aluno');
        $plan = Plan::create([
            'name' => 'Free Mobile',
            'type' => 'student',
            'price' => 0,
        ]);
        $plan->forceFill(['is_active' => true])->save();

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/subscriptions/checkout', ['plan_id' => $plan->id])
            ->assertOk()
            ->assertJsonPath('data.status', 'activated')
            ->assertJsonMissing(['app_return_links']);
    }

    public function test_app_subscription_return_redirects_to_deep_link(): void
    {
        $response = $this->get('/app/subscription/return/success');

        $response->assertRedirect('nexshape://subscription/success');
    }
}
