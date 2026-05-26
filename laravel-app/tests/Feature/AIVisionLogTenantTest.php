<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\AIVisionLog;
use App\Models\Clinic;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIVisionLogTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_vision_log_stores_academy_company_id_on_create(): void
    {
        $company = AcademyCompany::create(['name' => 'Vision Co', 'slug' => 'vision-co-tenant']);
        $clinic = Clinic::create([
            'academy_company_id' => $company->id,
            'name' => 'Clínica Vision',
            'slug' => 'clinica-vision-tenant',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'clinic_id' => $clinic->id,
            'academy_company_id' => $company->id,
        ]);

        $this->actingAs($user);
        TenantContext::set($clinic->id);

        $log = AIVisionLog::create([
            'user_id' => $user->id,
            'document_type' => 'workout_sheet',
            'confidence' => 0.9,
        ]);

        $this->assertSame($clinic->id, (int) $log->clinic_id);
        $this->assertSame($company->id, (int) $log->academy_company_id);
    }
}
