<?php

namespace App\Services;

use App\Models\BugIncident;
use App\Models\BugIncidentEvent;
use App\Models\SystemError;
use App\Models\User;
use App\Jobs\AnalyzeBugIncidentJob;
use App\Support\AppVersion;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class BugIncidentService
{
    public function __construct(
        private readonly SystemErrorFingerprintService $fingerprints,
        private readonly BugAgentContextService $agentContext,
        private readonly BugAgentCursorBridgeService $cursorBridge,
    ) {}

    /** @var array<string, list<string>> */
    private const TRANSITIONS = [
        BugIncident::STATE_RECEIVED => [BugIncident::STATE_INVESTIGATING],
        BugIncident::STATE_INVESTIGATING => [BugIncident::STATE_DIAGNOSIS_READY],
        BugIncident::STATE_DIAGNOSIS_READY => [BugIncident::STATE_IMPACT_ANALYZED],
        BugIncident::STATE_IMPACT_ANALYZED => [BugIncident::STATE_PATCH_PROPOSED],
        BugIncident::STATE_PATCH_PROPOSED => [BugIncident::STATE_WAITING_APPROVAL],
        BugIncident::STATE_WAITING_APPROVAL => [
            BugIncident::STATE_APPROVED,
            BugIncident::STATE_INVESTIGATING,
            BugIncident::STATE_PATCH_PROPOSED,
        ],
        BugIncident::STATE_APPROVED => [BugIncident::STATE_PATCH_APPLIED],
        BugIncident::STATE_PATCH_APPLIED => [BugIncident::STATE_TESTING],
        BugIncident::STATE_TESTING => [BugIncident::STATE_READY_FOR_STAGING],
        BugIncident::STATE_READY_FOR_STAGING => [BugIncident::STATE_WAITING_DEPLOY_APPROVAL],
        BugIncident::STATE_WAITING_DEPLOY_APPROVAL => [BugIncident::STATE_DEPLOYED],
        BugIncident::STATE_DEPLOYED => [
            BugIncident::STATE_MONITORING,
            BugIncident::STATE_ROLLED_BACK,
        ],
        BugIncident::STATE_MONITORING => [
            BugIncident::STATE_CLOSED,
            BugIncident::STATE_ROLLED_BACK,
        ],
        BugIncident::STATE_CLOSED => [BugIncident::STATE_ROLLED_BACK],
    ];

    public function generateIncidentCode(): string
    {
        $year = now()->format('Y');

        return DB::transaction(function () use ($year) {
            $last = BugIncident::query()
                ->where('incident_code', 'like', "BUG-{$year}-%")
                ->orderByDesc('id')
                ->lockForUpdate()
                ->value('incident_code');

            $seq = 1;
            if ($last && preg_match('/BUG-\d{4}-(\d+)/', $last, $m)) {
                $seq = ((int) $m[1]) + 1;
            }

            return sprintf('BUG-%s-%05d', $year, $seq);
        });
    }

    public function upsertFromSystemError(SystemError $error, ?User $openedBy = null): BugIncident
    {
        $fingerprint = $this->fingerprints->compute($error);
        $error->loadMissing('user');
        $user = $error->user;
        $critical = $this->detectCritical($error);

        $existing = BugIncident::query()
            ->where('fingerprint', $fingerprint)
            ->whereNull('ignored_at')
            ->whereNotIn('state', [BugIncident::STATE_CLOSED, BugIncident::STATE_ROLLED_BACK])
            ->first();

        if ($existing) {
            return $this->recordOccurrence($existing, $error, $user);
        }

        $now = $error->created_at ?? now();
        $incident = BugIncident::create([
            'incident_code' => $this->generateIncidentCode(),
            'fingerprint' => $fingerprint,
            'title' => $this->truncate($error->message, 120),
            'state' => BugIncident::STATE_RECEIVED,
            'severity' => $critical['severity'],
            'environment' => config('app.env', 'production'),
            'module' => $this->inferModule($error),
            'system_error_id' => $error->id,
            'affected_user_id' => $error->user_id,
            'affected_role' => $this->resolveRole($user),
            'clinic_id' => $user?->clinic_id,
            'route_path' => $error->url,
            'http_method' => $error->method,
            'method_name' => $this->fingerprints->stackMethod($error),
            'user_message' => $error->message,
            'error_summary' => $error->message,
            'stack_trace' => $error->stack_trace,
            'first_occurred_at' => $now,
            'last_occurred_at' => $now,
            'occurrence_count' => 1,
            'affected_users_count' => $error->user_id ? 1 : 0,
            'affected_clinics_count' => $user?->clinic_id ? 1 : 0,
            'log_reference' => 'system_errors:' . $error->id,
            'production_commit' => env('DEPLOY_COMMIT') ?: env('GIT_COMMIT'),
            'is_critical' => $critical['is_critical'],
            'requires_dual_approval' => $critical['requires_dual_approval'],
            'evidence' => [[
                'type' => 'system_error',
                'system_error_id' => $error->id,
                'message' => $error->message,
                'at' => $error->created_at?->toIso8601String(),
            ]],
            'opened_by' => $openedBy?->id,
        ]);

        $this->logEvent($incident, BugIncidentEvent::BUG_RECEIVED, ['system_error_id' => $error->id], $openedBy?->id);

        return $incident;
    }

    /** @deprecated Use upsertFromSystemError */
    public function createFromSystemError(SystemError $error, ?User $openedBy = null): BugIncident
    {
        return $this->upsertFromSystemError($error, $openedBy);
    }

    public function startAgentAnalysis(BugIncident $incident, SystemError $error, ?User $actor = null): BugIncident
    {
        $context = $this->agentContext->build($error, $incident, $actor);

        $incident->agent_context = $context;
        $incident->analysis_checklist = $this->agentContext->syncChecklistFromPack(
            $this->agentContext->defaultChecklist(),
            $context['context_pack'] ?? []
        );
        $incident->production_commit = $context['production_commit'] ?? $incident->production_commit;
        $incident->save();

        if ($incident->state === BugIncident::STATE_RECEIVED) {
            $incident = $this->transition($incident, BugIncident::STATE_INVESTIGATING);
        }

        $this->logEvent($incident, BugIncidentEvent::AI_ANALYSIS_STARTED, [
            'mode' => 'read_only',
            'context_pack_files' => $context['context_pack']['stats']['files_in_pack'] ?? null,
        ], $actor?->id);

        AnalyzeBugIncidentJob::dispatch($incident->id)->onQueue(
            config('services.bug_surgeon.queue', 'default')
        );

        return $incident->fresh();
    }

    public function ignore(BugIncident $incident, ?User $actor = null): BugIncident
    {
        $incident->ignored_at = now();
        $incident->save();
        $this->logEvent($incident, BugIncidentEvent::BUG_IGNORED, [], $actor?->id);

        return $incident;
    }

    public function summaryForUi(BugIncident $incident): array
    {
        return [
            'id' => $incident->id,
            'incident_code' => $incident->incident_code,
            'status' => $incident->statusLabel(),
            'state' => $incident->state,
            'action_group' => $incident->uiActionGroup(),
            'confidence' => $incident->confidence,
            'risk_level' => $incident->risk_level,
            'occurrence_count' => $incident->occurrence_count,
            'file_path' => $incident->file_path,
            'line_start' => $incident->line_start,
            'proposed_diff' => $incident->proposed_diff,
            'is_critical' => $incident->is_critical,
            'requires_dual_approval' => $incident->requires_dual_approval,
            'can_approve_patch' => $incident->canApprovePatch(),
            'checklist' => $incident->analysis_checklist ?? [],
            'show_url' => route('admin.bug-surgeon.show', $incident),
            'cursor' => $this->cursorBridge->commandsFor($incident),
            'sync_cursor_url' => route('admin.bug-surgeon.sync-cursor', $incident),
            'context_pack_summary' => [
                'files' => $incident->agent_context['context_pack']['stats']['files_in_pack'] ?? null,
                'primary' => $incident->agent_context['context_pack']['primary_frame']['repo_path'] ?? null,
                'method' => $incident->agent_context['context_pack']['primary_frame']['method'] ?? null,
            ],
            'auto_diagnosis' => [
                'enabled' => (bool) config('services.bug_surgeon.auto_diagnosis', true),
                'completed_at' => $incident->agent_context['auto_diagnosis_at'] ?? null,
                'awaiting' => $incident->state === BugIncident::STATE_INVESTIGATING
                    && config('services.bug_surgeon.auto_diagnosis', true)
                    && blank($incident->diagnosis),
            ],
        ];
    }

    private function recordOccurrence(BugIncident $incident, SystemError $error, ?User $user): BugIncident
    {
        $evidence = $incident->evidence ?? [];
        $evidence[] = [
            'type' => 'system_error',
            'system_error_id' => $error->id,
            'message' => $error->message,
            'at' => $error->created_at?->toIso8601String(),
        ];

        $userIds = collect($evidence)->pluck('system_error_id')->filter()->unique()->count();
        $clinicIds = $user?->clinic_id ? max($incident->affected_clinics_count, 1) : $incident->affected_clinics_count;

        $incident->update([
            'last_occurred_at' => $error->created_at ?? now(),
            'occurrence_count' => ($incident->occurrence_count ?? 0) + 1,
            'system_error_id' => $error->id,
            'stack_trace' => $error->stack_trace ?? $incident->stack_trace,
            'evidence' => $evidence,
            'affected_users_count' => max($incident->affected_users_count, $userIds),
            'affected_clinics_count' => $clinicIds,
        ]);

        return $incident->fresh();
    }

    public function logEvent(BugIncident $incident, string $type, array $payload = [], ?int $userId = null): void
    {
        BugIncidentEvent::create([
            'bug_incident_id' => $incident->id,
            'event_type' => $type,
            'payload' => $payload,
            'user_id' => $userId,
            'created_at' => now(),
        ]);
    }

    public function transition(BugIncident $incident, string $newState): BugIncident
    {
        $current = $incident->state;
        $allowed = self::TRANSITIONS[$current] ?? [];

        if (! in_array($newState, $allowed, true)) {
            throw new InvalidArgumentException(
                "Transição inválida: {$current} → {$newState}"
            );
        }

        $incident->state = $newState;
        $incident->save();

        return $incident->fresh();
    }

    public function updateAnalysis(BugIncident $incident, array $data): BugIncident
    {
        $incident->fill([
            'error_summary' => $data['error_summary'] ?? $incident->error_summary,
            'diagnosis' => $data['diagnosis'] ?? $incident->diagnosis,
            'root_cause' => $data['root_cause'] ?? $incident->root_cause,
            'module' => $data['module'] ?? $incident->module,
            'file_path' => $data['file_path'] ?? $incident->file_path,
            'line_start' => $data['line_start'] ?? $incident->line_start,
            'line_end' => $data['line_end'] ?? $incident->line_end,
            'code_snippet' => $data['code_snippet'] ?? $incident->code_snippet,
            'proposed_diff' => $data['proposed_diff'] ?? $incident->proposed_diff,
            'confidence' => $data['confidence'] ?? $incident->confidence,
            'error_reproduced' => $data['error_reproduced'] ?? $incident->error_reproduced,
            'risk_level' => $data['risk_level'] ?? $incident->risk_level,
            'evidence' => $data['evidence'] ?? $incident->evidence,
            'impact_analysis' => $data['impact_analysis'] ?? $incident->impact_analysis,
            'affected_role' => $data['affected_role'] ?? $incident->affected_role,
        ]);

        $incident->analysis_checklist = $this->syncChecklist($incident);

        if (filled($incident->proposed_diff)) {
            $incident->approved_change_hash = hash('sha256', str_replace("\r\n", "\n", $incident->proposed_diff));
        }

        $incident->save();

        return $incident->fresh();
    }

    public function advanceToWaitingApproval(BugIncident $incident): BugIncident
    {
        if (! filled($incident->proposed_diff) || ! filled($incident->file_path)) {
            throw new RuntimeException('Diff e arquivo são obrigatórios antes de aguardar aprovação.');
        }

        $targetIndex = array_search(BugIncident::STATE_WAITING_APPROVAL, BugIncident::ORDERED_STATES, true);
        $currentIndex = array_search($incident->state, BugIncident::ORDERED_STATES, true);

        if ($currentIndex === false || $targetIndex === false) {
            throw new RuntimeException('Estado inválido para avançar até aprovação.');
        }

        while ($currentIndex < $targetIndex) {
            $next = BugIncident::ORDERED_STATES[$currentIndex + 1];
            $incident = $this->transition($incident, $next);
            $currentIndex++;
        }

        return $incident;
    }

    public function approvePatch(BugIncident $incident, User $admin): BugIncident
    {
        if (! $incident->canApprovePatch()) {
            throw new RuntimeException('Incidente não está pronto para aprovação de correção.');
        }

        $authorization = $this->buildAuthorization($incident);

        $incident->authorization = $authorization;
        $incident->approved_change_hash = $authorization['approved_diff_hash'];
        $incident->approved_commit = $authorization['approved_commit'] ?? $incident->production_commit;
        $incident->approval_patch = true;
        $incident->approved_by = $admin->id;
        $incident->approved_at = now();
        $incident->branch_name = 'bugfix/' . strtolower($incident->incident_code) . '-patch';
        $incident->save();

        $this->logEvent($incident, BugIncidentEvent::PATCH_APPROVED, [
            'approved_diff_hash' => $authorization['approved_diff_hash'],
        ], $admin->id);

        return $this->transition($incident, BugIncident::STATE_APPROVED);
    }

    public function rejectPatch(BugIncident $incident): BugIncident
    {
        if ($incident->state !== BugIncident::STATE_WAITING_APPROVAL) {
            throw new RuntimeException('Somente incidentes aguardando aprovação podem ser rejeitados.');
        }

        $incident->approval_patch = false;
        $incident->authorization = null;
        $incident->save();

        return $this->transition($incident, BugIncident::STATE_PATCH_PROPOSED);
    }

    public function requestReanalysis(BugIncident $incident): BugIncident
    {
        if ($incident->state !== BugIncident::STATE_WAITING_APPROVAL) {
            throw new RuntimeException('Nova análise só a partir de WAITING_APPROVAL.');
        }

        $incident->approval_patch = false;
        $incident->authorization = null;
        $incident->proposed_diff = null;
        $incident->approved_change_hash = null;
        $incident->save();

        return $this->transition($incident, BugIncident::STATE_INVESTIGATING);
    }

    public function approveStaging(BugIncident $incident, User $admin): BugIncident
    {
        if (! $incident->approval_patch) {
            throw new RuntimeException('Correção deve ser aprovada antes do staging.');
        }

        if (! in_array($incident->state, [
            BugIncident::STATE_READY_FOR_STAGING,
            BugIncident::STATE_WAITING_DEPLOY_APPROVAL,
        ], true)) {
            throw new RuntimeException('Incidente não está pronto para aprovação de staging.');
        }

        $incident->approval_staging = true;
        $incident->save();

        if ($incident->state === BugIncident::STATE_READY_FOR_STAGING) {
            return $this->transition($incident, BugIncident::STATE_WAITING_DEPLOY_APPROVAL);
        }

        return $incident->fresh();
    }

    public function approveProduction(BugIncident $incident, User $admin): BugIncident
    {
        if (! $incident->approval_staging) {
            throw new RuntimeException('Staging deve ser aprovado antes da produção.');
        }

        if ($incident->state !== BugIncident::STATE_WAITING_DEPLOY_APPROVAL) {
            throw new RuntimeException('Incidente não está aguardando deploy.');
        }

        $incident->approval_production = true;
        $incident->deployed_at = now();
        $incident->save();

        $incident = $this->transition($incident, BugIncident::STATE_DEPLOYED);

        return $this->transition($incident, BugIncident::STATE_MONITORING);
    }

    public function markRollback(BugIncident $incident, string $reason): BugIncident
    {
        if (! filled($incident->commit_hash)) {
            $incident->rollback_command = null;
        } else {
            $incident->rollback_command = 'git revert ' . $incident->commit_hash;
        }

        $incident->approval_production = false;
        $incident->save();

        if (in_array($incident->state, [BugIncident::STATE_DEPLOYED, BugIncident::STATE_MONITORING], true)) {
            return $this->transition($incident, BugIncident::STATE_ROLLED_BACK);
        }

        $incident->state = BugIncident::STATE_ROLLED_BACK;
        $incident->save();

        return $incident->fresh();
    }

    public function recordCommit(BugIncident $incident, string $commitHash): BugIncident
    {
        $incident->commit_hash = $commitHash;
        $incident->rollback_command = 'git revert ' . $commitHash;
        $incident->save();

        if ($incident->state === BugIncident::STATE_APPROVED) {
            $incident = $this->transition($incident, BugIncident::STATE_PATCH_APPLIED);
        }

        if ($incident->state === BugIncident::STATE_PATCH_APPLIED) {
            $incident = $this->transition($incident, BugIncident::STATE_TESTING);
        }

        return $incident;
    }

    public function markCiPassed(BugIncident $incident): BugIncident
    {
        if ($incident->state === BugIncident::STATE_READY_FOR_STAGING) {
            return $incident;
        }

        if ($incident->state !== BugIncident::STATE_TESTING) {
            throw new RuntimeException('CI só pode ser aprovado quando o incidente está em TESTING.');
        }

        return $this->transition($incident, BugIncident::STATE_READY_FOR_STAGING);
    }

    /** @return array<string, mixed> */
    public function buildAuthorization(BugIncident $incident): array
    {
        if (! filled($incident->file_path) || ! filled($incident->proposed_diff)) {
            throw new RuntimeException('Arquivo e diff são obrigatórios para autorização.');
        }

        $diff = str_replace("\r\n", "\n", $incident->proposed_diff);
        $filePath = ltrim(str_replace('\\', '/', $incident->file_path), '/');

        return [
            'bug_id' => $incident->incident_code,
            'authorization_id' => $incident->incident_code,
            'approved_at' => now()->toIso8601String(),
            'approved_by' => 'human',
            'approved_commit' => substr((string) ($incident->production_commit ?? ''), 0, 12),
            'approved_files' => [$filePath],
            'approved_lines' => [
                $filePath => [
                    'start' => $incident->line_start ?? 1,
                    'end' => $incident->line_end ?? $incident->line_start ?? 1,
                ],
            ],
            'approved_diff' => $diff,
            'approved_change_hash' => hash('sha256', $diff),
            'approved_diff_hash' => hash('sha256', $diff),
            'max_files' => 1,
            'max_files_changed' => 1,
            'max_lines_changed' => 10,
            'allow_new_files' => false,
            'allow_delete_files' => false,
            'allow_migrations' => false,
            'allow_database_changes' => false,
            'allow_dependencies' => false,
            'allow_dependency_changes' => false,
            'allow_routes' => false,
            'allow_permissions' => false,
            'forbidden_patterns' => [
                'tenant_id',
                'Gate::',
                'middleware(',
                'Schema::',
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function buildSessionExport(BugIncident $incident): array
    {
        return [
            'incident_id' => $incident->incident_code,
            'state' => $incident->state,
            'severity' => $incident->severity,
            'module' => $incident->module,
            'confidence' => $incident->confidence,
            'approval_patch' => $incident->approval_patch,
            'approval_staging' => $incident->approval_staging,
            'approval_production' => $incident->approval_production,
            'opened_at' => $incident->created_at?->toIso8601String(),
            'rollback' => [
                'rollback_available' => filled($incident->commit_hash),
                'commit_hash' => $incident->commit_hash,
                'command' => $incident->rollback_command,
            ],
        ];
    }

    private function inferSeverity(SystemError $error): string
    {
        return $this->detectCritical($error)['severity'];
    }

    /** @return array{is_critical: bool, requires_dual_approval: bool, severity: string} */
    private function detectCritical(SystemError $error): array
    {
        $blob = strtolower(implode(' ', [
            (string) $error->message,
            (string) $error->stack_trace,
            (string) $error->url,
            $error->type,
        ]));

        $criticalPatterns = [
            'tenant', 'clinic_id', 'unauthorized', 'forbidden', 'vazamento',
            'payment', 'cobran', 'migrate', 'permission', 'prontuario', 'lgpd',
            'delete from', 'drop table', 'auth failed',
        ];

        foreach ($criticalPatterns as $pattern) {
            if (str_contains($blob, $pattern)) {
                return [
                    'is_critical' => true,
                    'requires_dual_approval' => true,
                    'severity' => 'critical',
                ];
            }
        }

        return [
            'is_critical' => false,
            'requires_dual_approval' => false,
            'severity' => match ($error->type) {
                'sql', 'auth' => 'high',
                'validation' => 'low',
                default => 'medium',
            },
        ];
    }

    private function inferModule(SystemError $error): ?string
    {
        $path = (string) parse_url((string) $error->url, PHP_URL_PATH);

        return match (true) {
            str_contains($path, 'avaliac') => 'Avaliação física',
            str_contains($path, 'aluno') => 'Portal aluno',
            str_contains($path, 'nutri') => 'Nutrição',
            str_contains($path, 'admin') => 'Admin',
            default => null,
        };
    }

    private function resolveRole(?User $user): ?string
    {
        if ($user === null) {
            return null;
        }

        if ($user->is_admin ?? false) {
            return 'admin';
        }

        return 'aluno';
    }

    /** @return list<array{key: string, label: string, done: bool}> */
    private function syncChecklist(BugIncident $incident): array
    {
        $checklist = $incident->analysis_checklist ?? $this->agentContext->defaultChecklist();
        $flags = [
            'route' => filled($incident->route_path),
            'controller' => filled($incident->file_path) && str_contains((string) $incident->file_path, 'Controller'),
            'service' => filled($incident->file_path) && str_contains((string) $incident->file_path, 'Service'),
            'reproduced' => (bool) $incident->error_reproduced,
            'root_cause' => filled($incident->root_cause) || filled($incident->diagnosis),
            'impact' => filled($incident->impact_analysis),
        ];

        foreach ($checklist as &$item) {
            if (isset($flags[$item['key']])) {
                $item['done'] = $flags[$item['key']];
            }
        }

        return $checklist;
    }

    private function truncate(?string $text, int $max): string
    {
        $text = (string) $text;
        if (strlen($text) <= $max) {
            return $text;
        }

        return substr($text, 0, $max - 1) . '…';
    }
}
