<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\Clinic;
use App\Models\User;
use App\Models\WorkoutImportLog;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutImportTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_workout_import_log_stores_tenant_columns_on_create(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Empresa Test',
            'slug' => 'empresa-tenant-test',
        ]);
        $clinic = Clinic::create([
            'academy_company_id' => $company->id,
            'name' => 'Clínica Tenant Test',
            'slug' => 'clinica-tenant-test',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'clinic_id' => $clinic->id,
            'academy_company_id' => $company->id,
        ]);

        TenantContext::set($clinic->id);
        $this->actingAs($user);

        $log = WorkoutImportLog::create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->assertSame($clinic->id, (int) $log->clinic_id);
        $this->assertSame($company->id, (int) $log->academy_company_id);
    }

    public function test_user_cannot_see_import_logs_from_another_clinic(): void
    {
        $companyA = AcademyCompany::create(['name' => 'Empresa A', 'slug' => 'empresa-a-tenant']);
        $companyB = AcademyCompany::create(['name' => 'Empresa B', 'slug' => 'empresa-b-tenant']);
        $clinicA = Clinic::create(['academy_company_id' => $companyA->id, 'name' => 'A', 'slug' => 'clinic-a-tenant', 'is_active' => true]);
        $clinicB = Clinic::create(['academy_company_id' => $companyB->id, 'name' => 'B', 'slug' => 'clinic-b-tenant', 'is_active' => true]);

        $userA = User::factory()->create(['clinic_id' => $clinicA->id, 'academy_company_id' => $companyA->id]);
        $userB = User::factory()->create(['clinic_id' => $clinicB->id, 'academy_company_id' => $companyB->id]);

        WorkoutImportLog::withoutGlobalScopes()->create([
            'user_id' => $userB->id,
            'clinic_id' => $clinicB->id,
            'academy_company_id' => $companyB->id,
            'status' => 'completed',
        ]);

        TenantContext::set($clinicA->id);
        $this->actingAs($userA);

        $this->assertCount(0, WorkoutImportLog::all());
    }
}
