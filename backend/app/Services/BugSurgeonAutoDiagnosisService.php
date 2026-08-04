<?php

namespace App\Services;

use App\Models\BugIncident;
use App\Models\BugIncidentEvent;
use App\Models\User;
use App\Services\AI\AIProviderService;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BugSurgeonAutoDiagnosisService
{
    private const AGENT_KEY = 'bug_surgeon';

    private const FORBIDDEN_DIFF_PATTERNS = [
        'tenant_id',
        'Schema::',
        'Gate::',
        'middleware(',
        'drop table',
        'delete from',
    ];

    public function __construct(
        private readonly AIProviderService $ai,
        private readonly BugSurgeonAgentReportService $reports,
        private readonly BugIncidentService $incidents,
    ) {}

    public function run(BugIncident $incident): BugIncident
    {
        if (! config('services.bug_surgeon.auto_diagnosis', true)) {
            throw new RuntimeException('Diagnóstico automático desabilitado.');
        }

        $incident->loadMissing('systemError');
        if ($incident->systemError === null) {
            throw new RuntimeException('Incidente sem erro vinculado.');
        }

        if ($incident->ignored_at !== null) {
            throw new RuntimeException('Incidente ignorado.');
        }

        if (! in_array($incident->state, [BugIncident::STATE_INVESTIGATING], true)) {
            throw new RuntimeException("Estado {$incident->state} não permite diagnóstico automático.");
        }

        if (empty(config('services.openai.api_key'))) {
            $this->incidents->logEvent($incident, BugIncidentEvent::AI_ANALYSIS_STARTED, [
                'mode' => 'auto_diagnosis_skipped',
                'reason' => 'openai_key_missing',
            ]);

            throw new RuntimeException('OpenAI API Key não configurada.');
        }

        $context = $incident->agent_context ?? [];
        $pack = $context['context_pack'] ?? [];
        if ($pack === []) {
            throw new RuntimeException('Context pack ausente. Execute a análise novamente.');
        }

        $user = $this->resolveActor($incident);
        $model = (string) config('services.bug_surgeon.ai_model', config('services.openai.model_fast', 'gpt-4o-mini'));

        $response = $this->ai->structuredCall(
            user: $user,
            messages: [
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user', 'content' => $this->userPrompt($incident, $context, $pack)],
            ],
            agentName: self::AGENT_KEY,
            modelName: $model,
            schemaName: 'bug_surgeon_diagnosis',
            schema: $this->diagnosisSchema(),
            context: [
                'feature_key' => 'bug_surgeon_diagnosis',
                'temperature' => 0.1,
                'max_tokens' => 3500,
                'incident_code' => $incident->incident_code,
            ],
        );

        if (! ($response['ok'] ?? false)) {
            $this->incidents->logEvent($incident, BugIncidentEvent::AI_ANALYSIS_STARTED, [
                'mode' => 'auto_diagnosis_failed',
                'error' => $response['error'] ?? 'unknown',
            ]);

            throw new RuntimeException($response['error'] ?? 'Falha na chamada de IA.');
        }

        $report = $this->parseAndValidateReport($response['message'] ?? '', $pack);
        $report['checklist'] = $this->mergeChecklist($incident, $report);
        $report['submit_for_approval'] = config('services.bug_surgeon.auto_submit_for_approval', true)
            && ($report['confidence'] ?? 0) >= (int) config('services.bug_surgeon.min_confidence', 80);

        if (($report['confidence'] ?? 0) < (int) config('services.bug_surgeon.min_confidence', 80)) {
            $report['force_low_confidence'] = true;
            $report['submit_for_approval'] = false;
        }

        $updated = $this->reports->applyReport($incident, $report);

        $meta = $updated->agent_context ?? [];
        $meta['auto_diagnosis_at'] = now()->toIso8601String();
        $meta['auto_diagnosis_model'] = $model;
        $updated->agent_context = $meta;
        $updated->save();

        $this->incidents->logEvent($updated, BugIncidentEvent::ROOT_CAUSE_IDENTIFIED, [
            'mode' => 'auto_diagnosis',
            'confidence' => $updated->confidence,
            'state' => $updated->state,
        ]);

        return $updated->fresh();
    }

    /** @param  array<string, mixed>  $pack
     * @return array<string, mixed>
     */
    public function parseAndValidateReport(string $json, array $pack): array
    {
        $data = json_decode($json, true);
        if (! is_array($data)) {
            throw new RuntimeException('Resposta da IA não é JSON válido.');
        }

        $allowedPaths = collect($pack['related_files'] ?? [])
            ->pluck('repo_path')
            ->filter()
            ->map(fn ($p) => str_replace('\\', '/', ltrim((string) $p, '/')))
            ->all();

        $filePath = str_replace('\\', '/', ltrim((string) ($data['file_path'] ?? ''), '/'));
        if ($filePath !== '' && $allowedPaths !== []) {
            $match = collect($allowedPaths)->contains(fn ($p) => $p === $filePath || str_ends_with($p, basename($filePath)));
            if (! $match) {
                Log::warning('BugSurgeon auto-diagnosis file_path outside pack', [
                    'file_path' => $filePath,
                    'allowed' => $allowedPaths,
                ]);
            }
        }

        $diff = (string) ($data['proposed_diff'] ?? '');
        foreach (self::FORBIDDEN_DIFF_PATTERNS as $pattern) {
            if (stripos($diff, $pattern) !== false) {
                throw new RuntimeException("Diff proposto contém padrão proibido: {$pattern}");
            }
        }

        if ($diff === '' || $filePath === '') {
            throw new RuntimeException('IA não retornou diff ou arquivo.');
        }

        $data['file_path'] = $filePath;
        $data['evidence'] = array_merge($data['evidence'] ?? [], [[
            'type' => 'auto_diagnosis',
            'detail' => 'Gerado por BugSurgeonAutoDiagnosisService',
        ]]);
        $data['impact_analysis'] = $data['impact_analysis'] ?? [
            'files_to_change' => [$filePath],
            'routes_changed' => false,
            'database_changes' => false,
            'permissions_changed' => false,
            'tenancy_preserved' => true,
        ];

        return $data;
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are the NexShape Bug Surgeon agent. Analyze production bugs using ONLY the provided context pack snippets.

Rules:
- Output must match the JSON schema exactly.
- Propose the MINIMAL unified diff (prefer one file, few lines).
- Do NOT suggest database migrations, permission changes, tenant/clinic logic, or refactors.
- Base root_cause on evidence in snippets and stack trace.
- proposed_diff must be unified diff format with ---/+++ headers.
- confidence 0-100; use >= 80 only when cause is clear from snippets.
- error_reproduced true if stack and code clearly explain the failure.
PROMPT;
    }

    /** @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $pack
     */
    private function userPrompt(BugIncident $incident, array $context, array $pack): string
    {
        $payload = [
            'incident_code' => $incident->incident_code,
            'error' => $context['error'] ?? null,
            'request' => $context['request'] ?? null,
            'primary_frame' => $pack['primary_frame'] ?? null,
            'route_hint' => $pack['route_hint'] ?? null,
            'git_suspects' => $pack['git_suspects'] ?? [],
            'related_files' => collect($pack['related_files'] ?? [])->map(fn ($f) => [
                'role' => $f['role'] ?? null,
                'repo_path' => $f['repo_path'] ?? null,
                'reason' => $f['reason'] ?? null,
                'line_start' => $f['line_start'] ?? null,
                'line_end' => $f['line_end'] ?? null,
                'snippet' => $f['snippet'] ?? null,
            ])->all(),
        ];

        return "Analyze this bug incident and return diagnosis JSON.\n\n"
            .json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** @return array<string, mixed> */
    private function diagnosisSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'problem_summary' => ['type' => 'string'],
                'diagnosis' => ['type' => 'string'],
                'root_cause' => ['type' => 'string'],
                'module' => ['type' => 'string'],
                'affected_role' => ['type' => 'string'],
                'file_path' => ['type' => 'string'],
                'method_name' => ['type' => 'string'],
                'line_start' => ['type' => 'integer'],
                'line_end' => ['type' => 'integer'],
                'code_snippet' => ['type' => 'string'],
                'proposed_diff' => ['type' => 'string'],
                'confidence' => ['type' => 'integer'],
                'error_reproduced' => ['type' => 'boolean'],
                'risk_level' => ['type' => 'string', 'enum' => ['low', 'medium', 'high', 'critical']],
            ],
            'required' => [
                'problem_summary',
                'diagnosis',
                'root_cause',
                'file_path',
                'method_name',
                'line_start',
                'line_end',
                'code_snippet',
                'proposed_diff',
                'confidence',
                'error_reproduced',
                'risk_level',
            ],
        ];
    }

    /** @param  array<string, mixed>  $report
     * @return list<array{key: string, label: string, done: bool}>
     */
    private function mergeChecklist(BugIncident $incident, array $report): array
    {
        $checklist = $incident->analysis_checklist ?? [];
        $doneKeys = ['log', 'stack', 'route', 'controller', 'service', 'root_cause', 'impact'];
        foreach ($checklist as &$item) {
            if (in_array($item['key'] ?? '', $doneKeys, true)) {
                $item['done'] = true;
            }
        }
        unset($item);

        if (($report['confidence'] ?? 0) >= 70) {
            foreach ($checklist as &$item) {
                if (($item['key'] ?? '') === 'reproduced') {
                    $item['done'] = (bool) ($report['error_reproduced'] ?? false);
                }
            }
            unset($item);
        }

        return $checklist;
    }

    private function resolveActor(BugIncident $incident): User
    {
        $configured = config('services.bug_surgeon.system_user_id');
        if ($configured) {
            $user = User::find((int) $configured);
            if ($user !== null) {
                return $user;
            }
        }

        if ($incident->opened_by) {
            $user = User::find($incident->opened_by);
            if ($user !== null) {
                return $user;
            }
        }

        if ($incident->affected_user_id) {
            $user = User::find($incident->affected_user_id);
            if ($user !== null) {
                return $user;
            }
        }

        $admin = User::query()->where('is_admin', true)->first();
        if ($admin !== null) {
            return $admin;
        }

        throw new RuntimeException('Nenhum usuário disponível para execução de IA do Bug Surgeon.');
    }
}
