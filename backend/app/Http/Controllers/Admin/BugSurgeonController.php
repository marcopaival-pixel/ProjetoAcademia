<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\BugIncident;
use App\Models\SystemError;
use App\Services\BugAgentCursorBridgeService;
use App\Services\BugIncidentService;
use App\Services\SystemErrorFingerprintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BugSurgeonController extends Controller
{
    public function __construct(
        private readonly BugIncidentService $incidents,
        private readonly SystemErrorFingerprintService $fingerprints,
        private readonly BugAgentCursorBridgeService $cursorBridge,
    ) {}

    public function index(): View
    {
        $incidents = BugIncident::query()
            ->with(['affectedUser', 'systemError'])
            ->orderByDesc('created_at')
            ->paginate(30);

        $stats = [
            'open' => BugIncident::query()->whereNotIn('state', [
                BugIncident::STATE_CLOSED,
                BugIncident::STATE_ROLLED_BACK,
            ])->count(),
            'waiting' => BugIncident::query()->where('state', BugIncident::STATE_WAITING_APPROVAL)->count(),
        ];

        return view('admin.bug_surgeon.index', compact('incidents', 'stats'));
    }

    public function show(BugIncident $incident): View
    {
        $incident->load(['affectedUser', 'systemError', 'openedBy', 'approvedByUser', 'events']);
        $cursor = $this->cursorBridge->commandsFor($incident);

        return view('admin.bug_surgeon.show', compact('incident', 'cursor'));
    }

    public function summary(BugIncident $incident): JsonResponse
    {
        return response()->json($this->incidents->summaryForUi($incident->fresh()));
    }

    public function startAnalysis(SystemError $systemError): RedirectResponse
    {
        $incident = $this->incidents->upsertFromSystemError($systemError, auth()->user());
        $incident = $this->incidents->startAgentAnalysis($incident, $systemError, auth()->user());

        try {
            $this->cursorBridge->exportToDirectory(
                $incident,
                $this->incidents->buildSessionExport($incident),
                null
            );
        } catch (\Throwable) {
            // Export local é best-effort; o operador pode usar sync manual.
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Iniciou análise com agente — {$incident->incident_code}",
            'ip_address' => request()->ip(),
            'payload' => [
                'bug_incident_id' => $incident->id,
                'system_error_id' => $systemError->id,
                'fingerprint' => $incident->fingerprint,
            ],
        ]);

        return redirect()
            ->route('admin.system-errors')
            ->with('bug_surgeon_drawer', $incident->id)
            ->with('success', "Análise iniciada — {$incident->incident_code} ({$incident->statusLabel()}).");
    }

    public function ignore(SystemError $systemError): RedirectResponse
    {
        $fingerprint = $this->fingerprints->compute($systemError);
        $incident = BugIncident::query()
            ->where('fingerprint', $fingerprint)
            ->whereNull('ignored_at')
            ->first();

        if ($incident) {
            $this->incidents->ignore($incident, auth()->user());
        }

        return back()->with('success', 'Erro marcado como ignorado para análise automática.');
    }

    /** @deprecated Use startAnalysis */
    public function storeFromSystemError(SystemError $systemError): RedirectResponse
    {
        return $this->startAnalysis($systemError);
    }

    public function updateAnalysis(Request $request, BugIncident $incident): RedirectResponse
    {
        $validated = $request->validate([
            'error_summary' => ['nullable', 'string', 'max:5000'],
            'module' => ['nullable', 'string', 'max:64'],
            'affected_role' => ['nullable', 'string', 'max:32'],
            'file_path' => ['nullable', 'string', 'max:500'],
            'line_start' => ['nullable', 'integer', 'min:1'],
            'line_end' => ['nullable', 'integer', 'min:1'],
            'code_snippet' => ['nullable', 'string'],
            'proposed_diff' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'root_cause' => ['nullable', 'string'],
            'error_reproduced' => ['nullable', 'boolean'],
            'confidence' => ['nullable', 'integer', 'min:0', 'max:100'],
            'risk_level' => ['nullable', 'string', 'in:low,medium,high'],
            'evidence_json' => ['nullable', 'string'],
            'impact_json' => ['nullable', 'string'],
            'submit_action' => ['nullable', 'string', 'in:save,submit_for_approval'],
        ]);

        $payload = collect($validated)->except(['evidence_json', 'impact_json', 'submit_action'])->all();

        if ($request->filled('evidence_json')) {
            $decoded = json_decode($request->input('evidence_json'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $payload['evidence'] = $decoded;
            }
        }

        if ($request->filled('impact_json')) {
            $decoded = json_decode($request->input('impact_json'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $payload['impact_analysis'] = $decoded;
            }
        }

        $this->incidents->updateAnalysis($incident, $payload);

        if (($validated['submit_action'] ?? 'save') === 'submit_for_approval') {
            try {
                $this->incidents->advanceToWaitingApproval($incident->fresh());
            } catch (RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return back()->with('success', 'Análise atualizada.');
    }

    public function approvePatch(BugIncident $incident): RedirectResponse
    {
        try {
            $this->incidents->approvePatch($incident, auth()->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Aprovou correção do incidente {$incident->incident_code}",
            'ip_address' => request()->ip(),
            'payload' => ['bug_incident_id' => $incident->id],
        ]);

        return back()->with('success', 'Correção aprovada. Exporte authorization.json para o Cursor.');
    }

    public function rejectPatch(BugIncident $incident): RedirectResponse
    {
        try {
            $this->incidents->rejectPatch($incident);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Correção rejeitada. Incidente voltou para proposta de patch.');
    }

    public function requestReanalysis(BugIncident $incident): RedirectResponse
    {
        try {
            $this->incidents->requestReanalysis($incident);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Nova análise solicitada. Estado: INVESTIGATING.');
    }

    public function approveStaging(BugIncident $incident): RedirectResponse
    {
        try {
            $this->incidents->approveStaging($incident, auth()->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Staging aprovado.');
    }

    public function approveProduction(BugIncident $incident): RedirectResponse
    {
        try {
            $this->incidents->approveProduction($incident, auth()->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Aprovou deploy em produção do incidente {$incident->incident_code}",
            'ip_address' => request()->ip(),
            'payload' => ['bug_incident_id' => $incident->id],
        ]);

        return back()->with('success', 'Produção aprovada. Execute deploy conforme runbook.');
    }

    public function rollback(BugIncident $incident): RedirectResponse
    {
        $this->incidents->markRollback($incident, request()->input('reason', ''));

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Rollback registrado para incidente {$incident->incident_code}",
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Rollback registrado.');
    }

    public function markCiPassed(BugIncident $incident): RedirectResponse
    {
        try {
            $this->incidents->markCiPassed($incident);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "CI aprovado para incidente {$incident->incident_code}",
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'CI marcado como aprovado. Estado: READY_FOR_STAGING.');
    }

    public function syncCursor(BugIncident $incident): RedirectResponse|JsonResponse
    {
        if (! is_dir(dirname($this->cursorBridge->defaultExportPath()))) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Diretório .cursor não encontrado na raiz do repositório.'], 422);
            }

            return back()->with('error', 'Diretório .cursor não encontrado na raiz do repositório.');
        }

        $authorization = ($incident->approval_patch && $incident->authorization)
            ? $incident->authorization
            : null;

        $dest = $this->cursorBridge->exportToDirectory(
            $incident,
            $this->incidents->buildSessionExport($incident),
            $authorization
        );

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Exportado para .cursor/bug-surgeon/',
                'path' => $dest,
                'cursor' => $this->cursorBridge->commandsFor($incident),
            ]);
        }

        return back()->with('success', 'JSONs exportados para .cursor/bug-surgeon/');
    }

    public function exportAuthorization(BugIncident $incident): StreamedResponse
    {
        if (! $incident->approval_patch || ! $incident->authorization) {
            abort(404, 'Autorização ainda não disponível.');
        }

        $filename = strtolower($incident->incident_code) . '-authorization.json';

        return response()->streamDownload(function () use ($incident) {
            echo json_encode($incident->authorization, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function exportSession(BugIncident $incident): StreamedResponse
    {
        $session = $this->incidents->buildSessionExport($incident);
        $filename = strtolower($incident->incident_code) . '-session.json';

        return response()->streamDownload(function () use ($session) {
            echo json_encode($session, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function recordCommit(Request $request, BugIncident $incident): RedirectResponse
    {
        $validated = $request->validate([
            'commit_hash' => ['required', 'string', 'max:40'],
        ]);

        $this->incidents->recordCommit($incident, $validated['commit_hash']);

        return back()->with('success', 'Commit registrado.');
    }
}
