<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicSettingsRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_clinic_settings_route_resolves_for_authenticated_user_with_company(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Empresa Teste',
            'slug' => 'empresa-teste-clinic',
        ]);

        $user = User::factory()->create([
            'academy_company_id' => $company->id,
        ]);

        $this->actingAs($user)
            ->get(route('clinic.settings'))
            ->assertOk()
            ->assertViewIs('clinic.settings');
    }

    public function test_clinic_settings_requires_authentication(): void
    {
        $this->get(route('clinic.settings'))->assertRedirect(route('login'));
    }
}
