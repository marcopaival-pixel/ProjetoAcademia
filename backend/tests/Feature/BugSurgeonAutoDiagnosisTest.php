<?php

namespace Tests\Feature;

use App\Models\BugIncident;
use App\Models\SystemError;
use App\Models\User;
use App\Services\BugSurgeonAgentReportService;
use App\Services\BugSurgeonAutoDiagnosisService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BugSurgeonAutoDiagnosisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.openai.api_key', 'test-key');
        Config::set('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        Config::set('services.bug_surgeon.auto_diagnosis', true);
        Config::set('services.bug_surgeon.auto_submit_for_approval', true);
        Config::set('services.bug_surgeon.min_confidence', 80);

        Schema::dropIfExists('bug_incident_events');
        Schema::dropIfExists('bug_incidents');
        Schema::dropIfExists('system_errors');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_admin')->default(true);
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
            $table->timestamps();
        });

        Schema::create('bug_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code', 32)->unique();
            $table->string('fingerprint', 64)->nullable();
            $table->string('title')->nullable();
            $table->string('state', 40)->default('INVESTIGATING');
            $table->string('severity', 16)->default('medium');
            $table->string('environment', 32)->default('testing');
            $table->string('module', 64)->nullable();
            $table->unsignedBigInteger('system_error_id')->nullable();
            $table->unsignedBigInteger('affected_user_id')->nullable();
            $table->string('affected_role', 32)->nullable();
            $table->string('route_path')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->string('method_name', 128)->nullable();
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
            $table->timestamp('ignored_at')->nullable();
            $table->unsignedBigInteger('opened_by')->nullable();
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

    public function test_parse_and_validate_report_accepts_valid_payload(): void
    {
        $service = app(BugSurgeonAutoDiagnosisService::class);
        $pack = [
            'related_files' => [
                ['repo_path' => 'backend/app/Http/Controllers/Api/V1/AssessmentController.php'],
            ],
        ];

        $json = json_encode([
            'problem_summary' => 'PDF falha',
            'diagnosis' => 'Coluna errada',
            'root_cause' => 'patient_id vs user_id',
            'file_path' => 'backend/app/Http/Controllers/Api/V1/AssessmentController.php',
            'method_name' => 'downloadPdf',
            'line_start' => 125,
            'line_end' => 125,
            'code_snippet' => '$x = 1;',
            'proposed_diff' => "--- a/file.php\n+++ b/file.php\n- old\n+ new",
            'confidence' => 95,
            'error_reproduced' => true,
            'risk_level' => 'low',
        ]);

        $report = $service->parseAndValidateReport($json, $pack);
        $this->assertSame(95, $report['confidence']);
        $this->assertStringContainsString('backend/app', $report['file_path']);
    }

    public function test_parse_rejects_forbidden_diff_patterns(): void
    {
        $service = app(BugSurgeonAutoDiagnosisService::class);
        $json = json_encode([
            'file_path' => 'backend/app/Foo.php',
            'proposed_diff' => 'Schema::create(',
        ]);

        $this->expectException(\RuntimeException::class);
        $service->parseAndValidateReport($json, ['related_files' => []]);
    }

    public function test_run_advances_incident_to_waiting_approval_with_mocked_openai(): void
    {
        $user = User::withoutEvents(function () {
            $u = new User(['name' => 'Admin', 'email' => 'admin-bug@test.local', 'is_admin' => true]);
            $u->password_hash = bcrypt('x');
            $u->save();

            return $u;
        });

        $error = SystemError::create([
            'type' => 'QueryException',
            'message' => 'Unknown column patient_id',
            'url' => '/api/v1/student/assessments/1/pdf',
            'method' => 'GET',
            'stack_trace' => 'at app/Http/Controllers/Api/V1/AssessmentController.php(125)',
        ]);

        $aiReport = [
            'problem_summary' => 'PDF quebrado',
            'diagnosis' => 'Usa patient_id',
            'root_cause' => 'Coluna inexistente',
            'module' => 'Avaliação física',
            'affected_role' => 'aluno',
            'file_path' => 'backend/app/Http/Controllers/Api/V1/AssessmentController.php',
            'method_name' => 'downloadPdf',
            'line_start' => 125,
            'line_end' => 125,
            'code_snippet' => '$assessment = BodyAssessment::where(...)',
            'proposed_diff' => "--- a/backend/app/Http/Controllers/Api/V1/AssessmentController.php\n+++ b/backend/app/Http/Controllers/Api/V1/AssessmentController.php\n- patient_id\n+ user_id",
            'confidence' => 96,
            'error_reproduced' => true,
            'risk_level' => 'low',
        ];

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => json_encode($aiReport)]]],
                'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 200, 'total_tokens' => 300],
            ], 200),
        ]);

        $incident = BugIncident::create([
            'incident_code' => 'BUG-2026-01001',
            'state' => BugIncident::STATE_INVESTIGATING,
            'system_error_id' => $error->id,
            'opened_by' => $user->id,
            'agent_context' => app(\App\Services\BugAgentContextService::class)->build($error, new BugIncident(['incident_code' => 'BUG-2026-01001'])),
        ]);

        $updated = app(BugSurgeonAutoDiagnosisService::class)->run($incident);

        $this->assertSame(BugIncident::STATE_WAITING_APPROVAL, $updated->state);
        $this->assertSame(96, $updated->confidence);
        $this->assertNotNull($updated->proposed_diff);
    }
}
