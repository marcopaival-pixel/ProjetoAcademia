<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\AiCreditPackage;
use App\Models\AiCreditTransaction;
use App\Models\Clinic;
use App\Models\Payment;
use App\Models\ReferralCode;
use App\Models\User;
use App\Policies\BodyAssessmentPolicy;
use App\Support\PatientAccessGuard;
use App\Services\Payment\PaymentProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class AuditRemediationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_admin_cannot_access_student_data_without_impersonation(): void
    {
        $admin = $this->userWithRole('professional', ['is_admin' => true]);
        $student = User::factory()->create();

        $this->assertFalse(PatientAccessGuard::canAccessStudentData($admin, $student->id));
    }

    public function test_admin_can_access_student_data_during_impersonation(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Empresa Audit',
            'slug' => 'empresa-audit',
        ]);

        $clinic = Clinic::create([
            'academy_company_id' => $company->id,
            'name' => 'Clínica Audit',
            'slug' => 'clinica-audit',
            'is_active' => true,
        ]);

        $admin = $this->userWithRole('professional', [
            'is_admin' => true,
            'academy_company_id' => $company->id,
            'clinic_id' => $clinic->id,
        ]);

        $student = User::factory()->create([
            'academy_company_id' => $company->id,
            'clinic_id' => $clinic->id,
        ]);

        session([
            'impersonated_clinic_id' => $clinic->id,
            'impersonated_company_id' => $company->id,
        ]);

        $this->assertTrue(PatientAccessGuard::canAccessStudentData($admin, $student->id));
    }

    public function test_body_assessment_policy_blocks_admin_without_impersonation(): void
    {
        $admin = $this->userWithRole('professional', ['is_admin' => true]);
        $student = User::factory()->create();

        $assessment = \App\Models\BodyAssessment::create([
            'user_id' => $student->id,
            'weight_kg' => 70,
            'assessment_date' => now()->toDateString(),
        ]);

        $policy = new BodyAssessmentPolicy;

        $this->assertFalse($policy->view($admin, $assessment));
    }

    public function test_payment_processor_is_idempotent_for_ai_credits(): void
    {
        $user = $this->userWithRole('aluno');
        $package = AiCreditPackage::create([
            'name' => 'Pacote Teste',
            'credits' => 100,
            'price' => 19.90,
            'is_active' => true,
        ]);

        $processor = app(PaymentProcessor::class);
        $payload = [
            'user_id' => $user->id,
            'gateway' => 'asaas',
            'gateway_id' => 'pay_test_123',
            'amount' => 19.90,
            'reference' => 'ai_credits:'.$package->id,
        ];

        $processor->processApproved($payload);
        $processor->processApproved($payload);

        $this->assertSame(1, Payment::where('gateway_id', 'pay_test_123')->count());
        $this->assertSame(1, AiCreditTransaction::where('user_id', $user->id)->where('reference_id', 'pay_test_123')->count());
    }

    public function test_referral_code_cannot_be_marked_used_twice(): void
    {
        $representative = $this->userWithRole('representative');
        $clinic = Clinic::create([
            'name' => 'Clínica Ref',
            'slug' => 'clinica-ref',
            'is_active' => true,
        ]);

        $code = ReferralCode::create([
            'code' => 'REP-TEST-0001',
            'representative_id' => $representative->id,
            'status' => ReferralCode::STATUS_DISPONIVEL,
        ]);

        $code->markAsUsed($clinic->id);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $code->markAsUsed($clinic->id);
    }
}
