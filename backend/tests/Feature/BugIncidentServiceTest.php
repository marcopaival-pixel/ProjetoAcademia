<?php

namespace Tests\Feature;

use App\Models\BugIncident;
use App\Models\SystemError;
use App\Services\BugIncidentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BugIncidentServiceTest extends TestCase
{
    private BugIncidentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('bug_incident_events');
        Schema::dropIfExists('bug_incidents');
        Schema::dropIfExists('system_errors');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('system_errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('type', 32);
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('message');
            $table->longText('stack_trace')->nullable();
            $table->json('payload')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('bug_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code', 32)->unique();
            $table->string('fingerprint', 64)->nullable();
            $table->string('title')->nullable();
            $table->string('state', 40)->default('RECEIVED')->index();
            $table->string('severity', 16)->default('medium');
            $table->string('environment', 32)->default('production');
            $table->string('module', 64)->nullable();
            $table->unsignedBigInteger('system_error_id')->nullable()->index();
            $table->unsignedBigInteger('affected_user_id')->nullable()->index();
            $table->string('affected_role', 32)->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->string('route_path')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->string('method_name', 128)->nullable();
            $table->text('user_message')->nullable();
            $table->text('error_summary')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('root_cause')->nullable();
            $table->longText('stack_trace')->nullable();
            $table->timestamp('first_occurred_at')->nullable();
            $table->timestamp('last_occurred_at')->nullable();
            $table->unsignedInteger('occurrence_count')->default(1);
            $table->unsignedInteger('affected_users_count')->default(0);
            $table->unsignedInteger('affected_clinics_count')->default(0);
            $table->string('log_reference')->nullable();
            $table->json('evidence')->nullable();
            $table->json('impact_analysis')->nullable();
            $table->json('agent_context')->nullable();
            $table->json('analysis_checklist')->nullable();
            $table->json('test_results')->nullable();
            $table->string('production_commit', 64)->nullable();
            $table->string('approved_commit', 64)->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('line_start')->nullable();
            $table->unsignedInteger('line_end')->nullable();
            $table->longText('code_snippet')->nullable();
            $table->longText('proposed_diff')->nullable();
            $table->string('approved_change_hash', 64)->nullable();
            $table->json('authorization')->nullable();
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->boolean('error_reproduced')->nullable();
            $table->string('risk_level', 16)->nullable();
            $table->boolean('is_critical')->default(false);
            $table->boolean('requires_dual_approval')->default(false);
            $table->timestamp('ignored_at')->nullable();
            $table->boolean('approval_patch')->default(false);
            $table->boolean('approval_staging')->default(false);
            $table->boolean('approval_production')->default(false);
            $table->string('staging_status', 32)->nullable();
            $table->string('production_status', 32)->nullable();
            $table->string('monitoring_status', 32)->nullable();
            $table->string('commit_hash', 40)->nullable();
            $table->string('branch_name')->nullable();
            $table->string('rollback_command')->nullable();
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bug_incident_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bug_incident_id');
            $table->string('event_type', 64);
            $table->json('payload')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        $this->service = app(BugIncidentService::class);
    }

    public function test_creates_incident_from_system_error_with_code(): void
    {
        $error = SystemError::create([
            'type' => 'validation',
            'message' => 'Erro ao salvar avaliação',
            'url' => '/aluno/avaliacoes',
            'method' => 'POST',
        ]);

        $incident = $this->service->createFromSystemError($error);

        $this->assertMatchesRegularExpression('/^BUG-\d{4}-\d{5}$/', $incident->incident_code);
        $this->assertSame(BugIncident::STATE_RECEIVED, $incident->state);
        $this->assertSame($error->id, $incident->system_error_id);
    }

    public function test_advance_to_waiting_approval_requires_diff_and_file(): void
    {
        $incident = BugIncident::create([
            'incident_code' => 'BUG-2026-00001',
            'state' => BugIncident::STATE_RECEIVED,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->service->advanceToWaitingApproval($incident);
    }

    public function test_approve_patch_builds_authorization_with_hash(): void
    {
        $diff = "- old\n+ new";
        $incident = BugIncident::create([
            'incident_code' => 'BUG-2026-00002',
            'state' => BugIncident::STATE_WAITING_APPROVAL,
            'file_path' => 'backend/app/Services/Example.php',
            'line_start' => 10,
            'line_end' => 10,
            'proposed_diff' => $diff,
            'approved_change_hash' => hash('sha256', $diff),
        ]);

        $user = new \App\Models\User(['id' => 1, 'name' => 'Admin', 'email' => 'a@test.com']);
        $user->id = 1;

        $approved = $this->service->approvePatch($incident, $user);

        $this->assertSame(BugIncident::STATE_APPROVED, $approved->state);
        $this->assertTrue($approved->approval_patch);
        $this->assertSame(hash('sha256', $diff), $approved->authorization['approved_diff_hash'] ?? $approved->authorization['approved_change_hash']);
        $this->assertContains('backend/app/Services/Example.php', $approved->authorization['approved_files']);
    }

    public function test_mark_ci_passed_advances_to_ready_for_staging(): void
    {
        $incident = BugIncident::create([
            'incident_code' => 'BUG-2026-00004',
            'state' => BugIncident::STATE_TESTING,
        ]);

        $updated = $this->service->markCiPassed($incident);

        $this->assertSame(BugIncident::STATE_READY_FOR_STAGING, $updated->state);
    }
}
