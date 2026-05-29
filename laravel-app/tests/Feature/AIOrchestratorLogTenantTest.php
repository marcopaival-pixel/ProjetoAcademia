<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\AIOrchestratorLog;
use App\Models\Clinic;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIOrchestratorLogTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_orchestrator_log_stores_academy_company_id_on_create(): void
    {
        $company = AcademyCompany::create(['name' => 'Orchestrator Co', 'slug' => 'orch-co-tenant']);
        $clinic = Clinic::create([
            'academy_company_id' => $company->id,
            'name' => 'Clínica Orch',
            'slug' => 'clinica-orch-tenant',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'clinic_id' => $clinic->id,
            'academy_company_id' => $company->id,
        ]);

        $this->actingAs($user);
        TenantContext::set($clinic->id);

        $log = AIOrchestratorLog::create([
            'user_id' => $user->id,
            'agent_name' => 'support',
            'user_message' => 'Olá',
            'ai_response' => 'Como posso ajudar?',
            'status' => 'success',
        ]);

        $this->assertSame($clinic->id, (int) $log->clinic_id);
        $this->assertSame($company->id, (int) $log->academy_company_id);
    }
}
