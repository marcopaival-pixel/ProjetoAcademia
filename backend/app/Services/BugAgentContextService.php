<?php

namespace App\Services;

use App\Models\BugIncident;
use App\Models\SystemError;
use App\Models\User;
use App\Support\AppVersion;
use Illuminate\Support\Facades\Schema;

class BugAgentContextService
{
    public function __construct(
        private readonly SystemErrorFingerprintService $fingerprints,
        private readonly BugLogMaskingService $masking,
        private readonly BugContextPackerService $packer,
    ) {}

    public function build(SystemError $error, BugIncident $incident, ?User $actor = null): array
    {
        $error->loadMissing('user');
        $user = $error->user;

        $payload = $this->masking->maskPayload($error->payload ?? []);
        $stack = $this->masking->mask($error->stack_trace);
        $message = $this->masking->mask($error->message);
        $contextPack = $this->packer->build($error, $incident);

        return [
            'incident_code' => $incident->incident_code,
            'fingerprint' => $incident->fingerprint,
            'generated_at' => now()->toIso8601String(),
            'environment' => config('app.env', 'production'),
            'app_version' => AppVersion::display(),
            'production_commit' => $this->resolveGitCommit(),
            'context_pack' => $contextPack,
            'request' => [
                'id' => request()->header('X-Request-ID') ?? ($error->payload['request_id'] ?? null),
                'method' => $error->method,
                'route' => $error->url,
                'ip' => $error->ip,
                'user_agent' => $error->user_agent,
                'payload_masked' => $payload,
            ],
            'user' => [
                'id' => $user?->id,
                'name' => $user?->name,
                'role' => $this->resolveUserRole($user),
                'clinic_id' => $user?->clinic_id ?? null,
            ],
            'error' => [
                'type' => $this->fingerprints->exceptionType($error),
                'message' => $message,
                'stack_trace' => $stack,
                'file' => $this->fingerprints->stackFile($error),
                'line' => $this->fingerprints->stackLine($error),
                'method' => $this->fingerprints->stackMethod($error),
                'system_error_id' => $error->id,
                'occurred_at' => $error->created_at?->toIso8601String(),
            ],
            'occurrence' => [
                'count' => $incident->occurrence_count,
                'first_at' => $incident->first_occurred_at?->toIso8601String(),
                'last_at' => $incident->last_occurred_at?->toIso8601String(),
                'affected_users' => $incident->affected_users_count,
                'affected_clinics' => $incident->affected_clinics_count,
            ],
            'cursor_skill' => 'nexshape-bug-surgeon',
            'mode' => 'read_only_investigation',
            'initiated_by' => $actor?->id,
        ];
    }

    /** @param  list<array{key: string, label: string, done: bool}>  $checklist
     * @param  array<string, mixed>  $pack
     * @return list<array{key: string, label: string, done: bool}>
     */
    public function syncChecklistFromPack(array $checklist, array $pack): array
    {
        $roles = collect($pack['related_files'] ?? [])->pluck('role')->unique()->all();
        $hasRoute = ! empty($pack['route_hint']['controller_repo_path'] ?? null)
            || ! empty($pack['route_hint']['path'] ?? null);

        foreach ($checklist as &$item) {
            match ($item['key']) {
                'route' => $item['done'] = $item['done'] || $hasRoute,
                'controller' => $item['done'] = $item['done'] || in_array('controller', $roles, true),
                'service' => $item['done'] = $item['done'] || in_array('service', $roles, true),
                default => null,
            };
        }
        unset($item);

        return $checklist;
    }

    /** @return list<array{key: string, label: string, done: bool}> */
    public function defaultChecklist(): array
    {
        return [
            ['key' => 'log', 'label' => 'Log carregado', 'done' => true],
            ['key' => 'stack', 'label' => 'Stack trace identificado', 'done' => true],
            ['key' => 'route', 'label' => 'Rota localizada', 'done' => false],
            ['key' => 'controller', 'label' => 'Controller localizado', 'done' => false],
            ['key' => 'service', 'label' => 'Service analisado', 'done' => false],
            ['key' => 'reproduced', 'label' => 'Erro reproduzido', 'done' => false],
            ['key' => 'root_cause', 'label' => 'Causa raiz encontrada', 'done' => false],
            ['key' => 'impact', 'label' => 'Impacto calculado', 'done' => false],
        ];
    }

    private function resolveGitCommit(): ?string
    {
        $fromEnv = env('DEPLOY_COMMIT') ?: env('GIT_COMMIT');
        if (is_string($fromEnv) && $fromEnv !== '') {
            return substr($fromEnv, 0, 12);
        }

        foreach ([base_path('../.git/HEAD'), base_path('.git/HEAD')] as $head) {
            if (! is_file($head)) {
                continue;
            }
            $ref = trim((string) file_get_contents($head));
            if (str_starts_with($ref, 'ref: ')) {
                $refPath = dirname($head) . '/' . trim(substr($ref, 5));
                if (is_file($refPath)) {
                    return substr(trim((string) file_get_contents($refPath)), 0, 12);
                }
            }

            return substr($ref, 0, 12);
        }

        return null;
    }

    private function resolveUserRole(?User $user): ?string
    {
        if ($user === null) {
            return null;
        }

        if ($user->is_admin ?? false) {
            return 'admin';
        }

        if (method_exists($user, 'roles') && $user->relationLoaded('roles') === false) {
            try {
                if (Schema::hasTable('user_roles')) {
                    $user->load('roles');
                }
            } catch (\Throwable) {
                //
            }
        }

        $role = $user->roles?->first()?->name ?? null;

        return $role ?: 'usuario';
    }
}
