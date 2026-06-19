<?php

namespace Tests\Feature;

use App\Models\AdminLog;
use App\Models\AuthAuditLog;
use App\Models\ClientErrorLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObservabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_observability_routes_require_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.observability.admin-logs'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.observability.auth-logs'))->assertForbidden();
    }

    public function test_admin_can_view_observability_pages(): void
    {
        $admin = User::factory()->administrator()->create();

        AdminLog::create([
            'user_id' => $admin->id,
            'action' => 'Teste auditoria',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $this->actingAs($admin)->get(route('admin.observability.admin-logs'))
            ->assertOk()
            ->assertSee('Teste auditoria', false);

        $this->actingAs($admin)->get(route('admin.observability.auth-logs'))->assertOk();
        $this->actingAs($admin)->get(route('admin.observability.api-logs'))->assertOk();
        $this->actingAs($admin)->get(route('admin.observability.client-errors'))->assertOk();
    }

    public function test_client_error_endpoint_accepts_report(): void
    {
        $this->postJson(route('api.v1.client-errors.store'), [
            'message' => 'Test JS error',
            'url' => 'https://example.com/page',
        ])->assertOk();

        $this->assertDatabaseHas('client_error_logs', [
            'message' => 'Test JS error',
        ]);
    }

    public function test_failed_login_creates_auth_audit_log(): void
    {
        $this->post('/login', [
            'email' => 'naoexiste@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertDatabaseHas('auth_audit_logs', [
            'event' => AuthAuditLog::EVENT_LOGIN_FAILED,
            'email' => 'naoexiste@example.com',
            'success' => false,
        ]);
    }

    public function test_successful_login_creates_auth_audit_log(): void
    {
        $user = User::factory()->create([
            'email' => 'login@test.com',
            'password_hash' => bcrypt('secret123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email' => 'login@test.com',
            'password' => 'secret123',
        ])->assertRedirect();

        $this->assertDatabaseHas('auth_audit_logs', [
            'event' => AuthAuditLog::EVENT_LOGIN_SUCCESS,
            'user_id' => $user->id,
            'success' => true,
        ]);
    }

    public function test_admin_can_export_admin_logs_csv(): void
    {
        $admin = User::factory()->administrator()->create();

        AdminLog::create([
            'user_id' => $admin->id,
            'action' => 'Export test',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $this->actingAs($admin)->get(route('admin.observability.admin-logs.export'))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_audit_report_command_generates_json(): void
    {
        $this->artisan('app:audit:report', ['--days' => 1])
            ->assertSuccessful();
    }
}
