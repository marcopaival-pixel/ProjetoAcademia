<?php

namespace Tests\Feature;

use App\Models\BugIncident;
use App\Models\SystemError;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BugSurgeonAgentApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.bug_surgeon.agent_token', 'test-agent-token-secret');

        Schema::dropIfExists('bug_incident_events');
        Schema::dropIfExists('bug_incidents');
        Schema::dropIfExists('system_errors');

        Schema::create('system_errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('type', 32);
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('message');
            $table->longText('stack_trace')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('bug_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code', 32)->unique();
            $table->string('fingerprint', 64)->nullable();
            $table->string('state', 40)->default('INVESTIGATING');
            $table->string('severity', 16)->default('medium');
            $table->string('environment', 32)->default('testing');
            $table->string('module', 64)->nullable();
            $table->unsignedBigInteger('system_error_id')->nullable();
            $table->string('affected_role', 32)->nullable();
            $table->text('error_summary')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('root_cause')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('line_start')->nullable();
            $table->unsignedInteger('line_end')->nullable();
            $table->longText('code_snippet')->nullable();
            $table->longText('proposed_diff')->nullable();
            $table->string('approved_change_hash', 64)->nullable();
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->boolean('error_reproduced')->nullable();
            $table->string('risk_level', 16)->nullable();
            $table->json('evidence')->nullable();
            $table->json('impact_analysis')->nullable();
            $table->json('agent_context')->nullable();
            $table->json('analysis_checklist')->nullable();
            $table->boolean('approval_patch')->default(false);
            $table->timestamp('ignored_at')->nullable();
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
    }

    public function test_agent_report_requires_token(): void
    {
        $incident = $this->makeIncident();

        $this->postJson("/admin/bug-surgeon/agent/incidents/{$incident->id}/report", [])
            ->assertUnauthorized();
    }

    public function test_agent_submits_report_and_advances_to_waiting_approval(): void
    {
        $incident = $this->makeIncident();

        $payload = [
            'diagnosis' => 'Problema no vínculo professional',
            'root_cause' => 'Acesso a relação nula',
            'file_path' => 'backend/app/Services/Example.php',
            'line_start' => 10,
            'proposed_diff' => "- old\n+ new",
            'confidence' => 95,
            'error_reproduced' => true,
            'risk_level' => 'low',
            'impact_analysis' => ['tenancy_preserved' => true],
            'submit_for_approval' => true,
        ];

        $this->withHeader('X-Bug-Surgeon-Token', 'test-agent-token-secret')
            ->postJson("/admin/bug-surgeon/agent/incidents/{$incident->id}/report", $payload)
            ->assertOk()
            ->assertJsonPath('incident.state', BugIncident::STATE_WAITING_APPROVAL);

        $incident->refresh();
        $this->assertSame('Problema no vínculo professional', $incident->diagnosis);
        $this->assertSame(95, $incident->confidence);
    }

    public function test_agent_context_by_code(): void
    {
        $incident = $this->makeIncident();

        $this->withHeader('X-Bug-Surgeon-Token', 'test-agent-token-secret')
            ->getJson('/admin/bug-surgeon/agent/incidents/by-code/BUG-2026-00099/context')
            ->assertOk()
            ->assertJsonPath('incident_code', 'BUG-2026-00099');
    }

    public function test_agent_marks_ci_passed_by_code(): void
    {
        $incident = $this->makeIncident();
        $incident->update(['state' => BugIncident::STATE_TESTING]);

        $this->withHeader('X-Bug-Surgeon-Token', 'test-agent-token-secret')
            ->postJson('/admin/bug-surgeon/agent/incidents/by-code/BUG-2026-00099/ci-passed', [
                'branch' => 'bugfix/bug-2026-00099-patch',
                'run_id' => 12345,
            ])
            ->assertOk()
            ->assertJsonPath('incident.state', BugIncident::STATE_READY_FOR_STAGING);
    }

    public function test_agent_ci_passed_is_idempotent_when_already_staged(): void
    {
        $incident = $this->makeIncident();
        $incident->update(['state' => BugIncident::STATE_READY_FOR_STAGING]);

        $this->withHeader('X-Bug-Surgeon-Token', 'test-agent-token-secret')
            ->postJson('/admin/bug-surgeon/agent/incidents/by-code/BUG-2026-00099/ci-passed')
            ->assertOk()
            ->assertJsonPath('incident.state', BugIncident::STATE_READY_FOR_STAGING);
    }

    private function makeIncident(): BugIncident
    {
        $error = SystemError::create([
            'type' => 'system',
            'message' => 'Test error',
            'url' => '/aluno/avaliacoes',
            'method' => 'POST',
        ]);

        return BugIncident::create([
            'incident_code' => 'BUG-2026-00099',
            'state' => BugIncident::STATE_INVESTIGATING,
            'system_error_id' => $error->id,
            'error_summary' => 'Test error',
        ]);
    }
}
