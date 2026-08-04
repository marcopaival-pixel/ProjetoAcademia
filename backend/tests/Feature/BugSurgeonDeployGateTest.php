<?php

namespace Tests\Feature;

use App\Models\BugIncident;
use App\Services\BugSurgeonDeployGateService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BugSurgeonDeployGateTest extends TestCase
{
    private BugSurgeonDeployGateService $gate;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('bug_incidents');
        Schema::create('bug_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code', 32)->unique();
            $table->string('state', 40)->default('RECEIVED');
            $table->string('severity', 16)->default('medium');
            $table->string('module', 64)->nullable();
            $table->unsignedBigInteger('system_error_id')->nullable();
            $table->unsignedBigInteger('affected_user_id')->nullable();
            $table->string('affected_role', 32)->nullable();
            $table->string('route_path')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->text('user_message')->nullable();
            $table->text('error_summary')->nullable();
            $table->longText('stack_trace')->nullable();
            $table->json('evidence')->nullable();
            $table->json('impact_analysis')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('line_start')->nullable();
            $table->unsignedInteger('line_end')->nullable();
            $table->longText('code_snippet')->nullable();
            $table->longText('proposed_diff')->nullable();
            $table->string('approved_change_hash', 64)->nullable();
            $table->json('authorization')->nullable();
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->string('risk_level', 16)->nullable();
            $table->boolean('approval_patch')->default(false);
            $table->boolean('approval_staging')->default(false);
            $table->boolean('approval_production')->default(false);
            $table->string('commit_hash', 40)->nullable();
            $table->string('branch_name')->nullable();
            $table->string('rollback_command')->nullable();
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        $this->gate = app(BugSurgeonDeployGateService::class);
    }

    public function test_extracts_incident_code_from_branch(): void
    {
        $this->assertSame(
            'BUG-2026-00042',
            $this->gate->extractIncidentCodeFromBranch('bugfix/BUG-2026-00042-null-professional')
        );
        $this->assertNull($this->gate->extractIncidentCodeFromBranch('feature/foo'));
    }

    public function test_staging_gate_blocks_without_approvals(): void
    {
        BugIncident::create([
            'incident_code' => 'BUG-2026-00042',
            'state' => BugIncident::STATE_TESTING,
            'approval_patch' => true,
            'commit_hash' => 'abc123',
        ]);

        $errors = $this->gate->validateDeploy('homologacao', 'bugfix/BUG-2026-00042-fix');

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('staging não aprovado', implode(' ', $errors));
    }

    public function test_staging_gate_passes_when_ready(): void
    {
        BugIncident::create([
            'incident_code' => 'BUG-2026-00043',
            'state' => BugIncident::STATE_READY_FOR_STAGING,
            'approval_patch' => true,
            'approval_staging' => true,
            'commit_hash' => 'def456',
        ]);

        $this->assertSame([], $this->gate->validateDeploy('homologacao', 'bugfix/BUG-2026-00043-fix'));
    }
}
