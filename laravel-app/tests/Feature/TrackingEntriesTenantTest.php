<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\Clinic;
use App\Models\FoodEntry;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingEntriesTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_food_entries_are_isolated_by_clinic(): void
    {
        $companyA = AcademyCompany::create(['name' => 'Empresa A', 'slug' => 'empresa-a-track']);
        $companyB = AcademyCompany::create(['name' => 'Empresa B', 'slug' => 'empresa-b-track']);

        $clinicA = Clinic::create([
            'academy_company_id' => $companyA->id,
            'name' => 'Clínica A',
            'slug' => 'clinica-a-track',
            'is_active' => true,
        ]);
        $clinicB = Clinic::create([
            'academy_company_id' => $companyB->id,
            'name' => 'Clínica B',
            'slug' => 'clinica-b-track',
            'is_active' => true,
        ]);

        $userA = User::factory()->create([
            'academy_company_id' => $companyA->id,
            'clinic_id' => $clinicA->id,
        ]);
        $userB = User::factory()->create([
            'academy_company_id' => $companyB->id,
            'clinic_id' => $clinicB->id,
        ]);

        FoodEntry::withoutGlobalScopes()->create([
            'user_id' => $userB->id,
            'clinic_id' => $clinicB->id,
            'academy_company_id' => $companyB->id,
            'entry_date' => now()->toDateString(),
            'meal_type' => 'lunch',
            'food_name' => 'Arroz',
            'calories' => 200,
        ]);

        $this->actingAs($userA);
        TenantContext::set($clinicA->id);

        $this->assertCount(0, FoodEntry::all());
    }

    public function test_food_entry_auto_fills_tenant_columns_on_create(): void
    {
        $company = AcademyCompany::create(['name' => 'Empresa Track', 'slug' => 'empresa-track-fill']);
        $clinic = Clinic::create([
            'academy_company_id' => $company->id,
            'name' => 'Clínica Track',
            'slug' => 'clinica-track-fill',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'academy_company_id' => $company->id,
            'clinic_id' => $clinic->id,
        ]);

        $this->actingAs($user);
        TenantContext::set($clinic->id);

        $entry = FoodEntry::create([
            'user_id' => $user->id,
            'entry_date' => now()->toDateString(),
            'meal_type' => 'breakfast',
            'food_name' => 'Ovos',
            'calories' => 150,
        ]);

        $this->assertSame($clinic->id, (int) $entry->clinic_id);
        $this->assertSame($company->id, (int) $entry->academy_company_id);
    }
}
