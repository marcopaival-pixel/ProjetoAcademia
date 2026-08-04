<?php

namespace App\Services;

use App\Models\BugIncident;
use Illuminate\Support\Facades\File;

class BugAgentCursorBridgeService
{
    /** @return array<string, mixed> */
    public function commandsFor(BugIncident $incident): array
    {
        $code = $incident->incident_code;
        $branch = $incident->branch_name ?? 'bugfix/'.strtolower($code).'-patch';

        return [
            'incident_code' => $code,
            'state' => $incident->state,
            'cursor_prompt' => "Use a skill nexshape-bug-surgeon. Analise o incidente {$code} em modo somente leitura. "
                .'Contexto: php artisan bug-surgeon:fetch-context '.$code,
            'fetch_context' => 'cd backend && php artisan bug-surgeon:fetch-context '.$code,
            'submit_analysis' => 'cd backend && php artisan bug-surgeon:submit-analysis '.$code
                .' --file=../.cursor/bug-surgeon/agent-report.json',
            'export_cursor' => 'cd backend && php artisan bug-surgeon:export-cursor '.$code,
            'sync_cursor_ps1' => '.\\scripts\\bug-surgeon-sync-cursor.ps1 -IncidentCode '.$code,
            'suggested_branch' => $branch,
            'agent_context_url' => url('/admin/bug-surgeon/agent/incidents/by-code/'.$code.'/context'),
        ];
    }

    public function defaultExportPath(): string
    {
        return base_path('../.cursor/bug-surgeon');
    }

    /** @param  array<string, mixed>  $session */
    public function exportToDirectory(BugIncident $incident, array $session, ?array $authorization = null, ?string $dest = null): string
    {
        $dest = $dest ?? $this->defaultExportPath();
        File::ensureDirectoryExists($dest);

        File::put(
            $dest.DIRECTORY_SEPARATOR.'session.json',
            json_encode($session, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
        );

        $authPath = $dest.DIRECTORY_SEPARATOR.'authorization.json';
        if ($authorization !== null) {
            File::put(
                $authPath,
                json_encode($authorization, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            );
        } elseif (File::exists($authPath)) {
            File::delete($authPath);
        }

        File::put(
            $dest.DIRECTORY_SEPARATOR.'cursor-commands.json',
            json_encode($this->commandsFor($incident), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
        );

        if (is_array($incident->agent_context['context_pack'] ?? null)) {
            File::put(
                $dest.DIRECTORY_SEPARATOR.'context_pack.json',
                json_encode($incident->agent_context['context_pack'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            );
        }

        return $dest;
    }
}
