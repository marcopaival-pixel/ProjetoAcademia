<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BugIncident;
use App\Services\BugAgentContextService;
use App\Services\BugIncidentService;
use App\Services\BugSurgeonAgentReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Endpoints consumidos pelo agente Cursor (token dedicado, sem sessão web).
 */
class BugSurgeonAgentController extends Controller
{
    public function __construct(
        private readonly BugIncidentService $incidents,
        private readonly BugAgentContextService $context,
        private readonly BugSurgeonAgentReportService $reports,
    ) {}

    public function context(BugIncident $incident): JsonResponse
    {
        $error = $incident->systemError;
        if ($error === null) {
            return response()->json([
                'message' => 'Incidente sem system_error vinculado.',
                'incident_code' => $incident->incident_code,
                'agent_context' => $incident->agent_context,
            ]);
        }

        $ctx = $incident->agent_context ?? $this->context->build($error, $incident);

        return response()->json([
            'incident_code' => $incident->incident_code,
            'state' => $incident->state,
            'status' => $incident->statusLabel(),
            'context' => $ctx,
            'context_pack' => $ctx['context_pack'] ?? null,
            'checklist' => $incident->analysis_checklist ?? $this->context->defaultChecklist(),
        ]);
    }

    public function contextByCode(string $code): JsonResponse
    {
        $incident = $this->resolveByCode($code);

        return $this->context($incident);
    }

    public function submitReport(Request $request, BugIncident $incident): JsonResponse
    {
        $validated = $request->validate([
            'problem_summary' => ['nullable', 'string', 'max:5000'],
            'diagnosis' => ['nullable', 'string', 'max:10000'],
            'root_cause' => ['nullable', 'string', 'max:10000'],
            'module' => ['nullable', 'string', 'max:64'],
            'affected_role' => ['nullable', 'string', 'max:32'],
            'file_path' => ['nullable', 'string', 'max:500'],
            'method_name' => ['nullable', 'string', 'max:128'],
            'line_start' => ['nullable', 'integer', 'min:1'],
            'line_end' => ['nullable', 'integer', 'min:1'],
            'code_snippet' => ['nullable', 'string'],
            'proposed_diff' => ['nullable', 'string'],
            'confidence' => ['nullable', 'integer', 'min:0', 'max:100'],
            'error_reproduced' => ['nullable', 'boolean'],
            'risk_level' => ['nullable', 'string', 'in:low,medium,high,critical'],
            'evidence' => ['nullable', 'array'],
            'impact_analysis' => ['nullable', 'array'],
            'checklist' => ['nullable', 'array'],
            'submit_for_approval' => ['nullable', 'boolean'],
            'force_low_confidence' => ['nullable', 'boolean'],
        ]);

        try {
            $updated = $this->reports->applyReport($incident, $validated);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Relatório do agente registrado.',
            'incident' => $this->incidents->summaryForUi($updated),
            'admin_url' => route('admin.bug-surgeon.show', $updated),
        ]);
    }

    public function submitReportByCode(Request $request, string $code): JsonResponse
    {
        return $this->submitReport($request, $this->resolveByCode($code));
    }

    public function markCiPassedByCode(Request $request, string $code): JsonResponse
    {
        $incident = $this->resolveByCode($code);

        try {
            $updated = $this->incidents->markCiPassed($incident);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $this->incidents->logEvent($updated, \App\Models\BugIncidentEvent::TESTS_APPROVED, [
            'branch' => $request->input('branch'),
            'run_id' => $request->input('run_id'),
            'source' => 'github_actions',
        ]);

        return response()->json([
            'message' => 'CI marcado como aprovado.',
            'incident' => $this->incidents->summaryForUi($updated),
        ]);
    }

    private function resolveByCode(string $code): BugIncident
    {
        $incident = BugIncident::query()
            ->where('incident_code', strtoupper($code))
            ->orderByDesc('id')
            ->first();

        if ($incident === null) {
            abort(404, 'Incidente não encontrado.');
        }

        return $incident;
    }
}
