<?php

namespace Tests\Feature;

use App\Models\AcademyCompany;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\User;
use App\Services\Lgpd\LgpdUserAnonymizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class LgpdUserAnonymizationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_anonymizes_user_with_payment_without_hard_delete(): void
    {
        $buyer = $this->userWithRole('aluno', ['status' => 'active']);

        Payment::create([
            'user_id' => $buyer->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'mp_lgpd_anon_test',
            'amount' => 50,
            'status' => 'paid',
        ]);

        $originalId = $buyer->id;
        $originalName = $buyer->name;
        $originalEmail = $buyer->email;

        app(LgpdUserAnonymizationService::class)->anonymize(
            $buyer,
            null,
            'Teste automatizado LGPD'
        );

        $buyer->refresh();

        $this->assertDatabaseHas('users', ['id' => $originalId]);
        $this->assertDatabaseMissing('users', ['id' => $originalId, 'email' => $originalEmail]);
        $this->assertTrue($buyer->isAnonymized());
        $this->assertSame(LgpdUserAnonymizationService::STATUS_ANONYMIZED, $buyer->status);
        $this->assertStringContainsString('anonimizado', $buyer->email);
        $this->assertNotSame($originalName, $buyer->name);

        $this->assertDatabaseHas('payments', [
            'user_id' => $originalId,
            'gateway_id' => 'mp_lgpd_anon_test',
        ]);

        $this->assertDatabaseHas('user_consents', [
            'user_id' => $originalId,
            'consent_type' => 'account_anonymization',
        ]);
    }

    public function test_anonymized_user_cannot_login(): void
    {
        $user = $this->userWithRole('aluno', [
            'status' => 'active',
            'email' => 'lgpd-login-test@example.com',
        ]);

        app(LgpdUserAnonymizationService::class)->anonymize($user);

        $this->post('/login', [
            'email' => 'lgpd-login-test@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_tenant_admin_cannot_access_platform_lgpd_export(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Empresa LGPD',
            'slug' => 'empresa-lgpd-export',
        ]);

        $tenantAdmin = $this->userWithRole('finance', [
            'is_admin' => false,
            'academy_company_id' => $company->id,
        ]);
        $tenantAdmin->permissions()->attach(
            Permission::query()->where('name', 'admin.access')->firstOrFail()->id
        );

        $target = User::factory()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($tenantAdmin)
            ->withSession(['active_role' => 'finance'])
            ->get(route('admin.lgpd.export-user', $target));

        $response->assertForbidden();
    }

    public function test_platform_admin_can_access_lgpd_export(): void
    {
        $company = AcademyCompany::create([
            'name' => 'Empresa LGPD Admin',
            'slug' => 'empresa-lgpd-admin',
        ]);

        $platformAdmin = User::factory()->administrator()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $target = User::factory()->create([
            'academy_company_id' => $company->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($platformAdmin)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.lgpd.export-user', $target));

        $response->assertOk();
    }
}
