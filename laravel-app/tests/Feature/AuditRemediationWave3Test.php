<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\Clinic;
use App\Models\MercadoPagoCredit;
use App\Models\MenuPermissionAuditLog;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Services\FinancialMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class AuditRemediationWave3Test extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_legacy_mp_revenue_excludes_rows_already_in_payments(): void
    {
        $user = User::factory()->create();

        MercadoPagoCredit::create([
            'mp_payment_id' => 90001,
            'user_id' => $user->id,
            'plan_code' => 'ai_credits',
            'transaction_amount' => 50.00,
        ]);

        MercadoPagoCredit::create([
            'mp_payment_id' => 90002,
            'user_id' => $user->id,
            'plan_code' => 'ai_credits',
            'transaction_amount' => 30.00,
        ]);

        Payment::create([
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_id' => '90001',
            'amount' => 50.00,
            'status' => 'paid',
        ]);

        $legacy = app(FinancialMetricsService::class)->legacyMercadoPagoRevenueExcludingPayments();

        $this->assertSame(30.0, $legacy);
    }

    public function test_menu_permission_audit_log_relates_to_role(): void
    {
        $role = Role::query()->firstOrFail();
        $admin = $this->userWithRole('professional', ['is_admin' => true]);

        $log = MenuPermissionAuditLog::create([
            'user_id' => $admin->id,
            'role_id' => $role->id,
            'action' => 'test.audit',
            'payload' => ['menus_updated' => 1],
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertTrue($log->role()->exists());
        $this->assertSame($role->id, $log->role->id);
        $this->assertSame($role->id, $log->profile->id);
    }

    public function test_medical_record_blocks_professional_from_other_tenant_patient(): void
    {
        $companyA = AcademyCompany::create(['name' => 'Clínica A', 'slug' => 'clinica-a-wave3']);
        $companyB = AcademyCompany::create(['name' => 'Clínica B', 'slug' => 'clinica-b-wave3']);

        $clinicA = Clinic::create([
            'academy_company_id' => $companyA->id,
            'name' => 'Unidade A',
            'slug' => 'unidade-a-wave3',
            'is_active' => true,
        ]);

        $professional = $this->userWithRole('professional', [
            'academy_company_id' => $companyA->id,
            'clinic_id' => $clinicA->id,
            'status' => 'active',
        ]);

        $patientOtherTenant = $this->userWithRole('aluno', [
            'academy_company_id' => $companyB->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($professional)
            ->get(route('professional.patients.medical-records.index', $patientOtherTenant));

        $this->assertContains($response->status(), [403, 404]);
    }

    public function test_referral_verify_returns_generic_error_for_invalid_code(): void
    {
        $this->postJson(route('api.v1.referral.verify'), [
            'code' => 'CODIGO-INEXISTENTE-XYZ',
        ])
            ->assertStatus(422)
            ->assertJson([
                'valid' => false,
            ])
            ->assertJsonMissing(['discount_amount']);
    }
}
