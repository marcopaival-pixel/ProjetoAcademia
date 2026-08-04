<?php

namespace Tests\Feature;

use App\Models\BugIncident;
use App\Models\SystemError;
use App\Services\BugContextPackerService;
use Tests\TestCase;

class BugContextPackerTest extends TestCase
{
    public function test_builds_context_pack_from_assessment_controller_stack(): void
    {
        $error = new SystemError([
            'type' => 'QueryException',
            'message' => 'Unknown column patient_id',
            'url' => '/api/v1/student/assessments/42/pdf',
            'method' => 'GET',
            'stack_trace' => <<<'TRACE'
Illuminate\Database\QueryException: Unknown column patient_id
  at vendor/laravel/framework/src/Illuminate/Database/Connection.php:825
  at app/Http/Controllers/Api/V1/AssessmentController.php(125): Illuminate\Database\Eloquent\Builder->find()
TRACE,
        ]);

        $incident = new BugIncident([
            'incident_code' => 'BUG-2026-00999',
            'state' => BugIncident::STATE_INVESTIGATING,
        ]);

        $pack = app(BugContextPackerService::class)->build($error, $incident);

        $this->assertSame(1, $pack['version']);
        $this->assertNotNull($pack['primary_frame']);
        $this->assertStringContainsString('AssessmentController.php', $pack['primary_frame']['repo_path']);
        $this->assertSame('downloadPdf', $pack['primary_frame']['method']);
        $this->assertGreaterThanOrEqual(1, $pack['stats']['files_in_pack']);

        $paths = collect($pack['related_files'])->pluck('repo_path')->all();
        $this->assertTrue(
            collect($paths)->contains(fn ($p) => str_contains($p, 'AssessmentController.php')),
            'Pack should include AssessmentController'
        );

        $this->assertSame('/api/v1/student/assessments/42/pdf', $pack['route_hint']['path'] ?? null);
    }

    public function test_related_files_include_snippets_not_full_project(): void
    {
        $error = new SystemError([
            'type' => 'system',
            'message' => 'Test',
            'url' => '/api/v1/assessments',
            'method' => 'POST',
            'stack_trace' => 'at app/Http/Controllers/Api/V1/AssessmentController.php(85): BodyAssessment::create()',
        ]);

        $incident = new BugIncident(['incident_code' => 'BUG-2026-00998']);

        $pack = app(BugContextPackerService::class)->build($error, $incident);

        $this->assertLessThanOrEqual(12, $pack['stats']['files_in_pack']);
        foreach ($pack['related_files'] as $file) {
            $this->assertArrayHasKey('snippet', $file);
            $this->assertLessThan(15000, strlen($file['snippet']));
        }
    }
}
