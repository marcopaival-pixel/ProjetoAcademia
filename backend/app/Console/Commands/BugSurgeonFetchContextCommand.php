<?php

namespace App\Console\Commands;

use App\Models\BugIncident;
use App\Services\BugAgentContextService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BugSurgeonFetchContextCommand extends Command
{
    protected $signature = 'bug-surgeon:fetch-context
                            {incident : incident_code ou id}
                            {--output= : caminho do JSON (default: ../.cursor/bug-surgeon/agent-context.json)}';

    protected $description = 'Exporta contexto mascarado do incidente para o agente Cursor';

    public function handle(BugAgentContextService $context): int
    {
        $incident = $this->resolveIncident((string) $this->argument('incident'));
        if ($incident === null) {
            $this->error('Incidente não encontrado.');

            return self::FAILURE;
        }

        $error = $incident->systemError;
        if ($error === null) {
            $this->error('Incidente sem erro de sistema vinculado.');

            return self::FAILURE;
        }

        $payload = [
            'incident_code' => $incident->incident_code,
            'state' => $incident->state,
            'context' => $incident->agent_context ?? $context->build($error, $incident),
            'checklist' => $incident->analysis_checklist ?? $context->defaultChecklist(),
        ];

        $out = $this->option('output')
            ?: base_path('../.cursor/bug-surgeon/agent-context.json');

        File::ensureDirectoryExists(dirname($out));
        File::put($out, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");

        $packPath = dirname($out).DIRECTORY_SEPARATOR.'context_pack.json';
        if (! empty($payload['context']['context_pack'])) {
            File::put(
                $packPath,
                json_encode($payload['context']['context_pack'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            );
            $this->line("Context pack → {$packPath}");
        }

        $this->info("Contexto exportado → {$out}");

        return self::SUCCESS;
    }

    private function resolveIncident(string $key): ?BugIncident
    {
        if (ctype_digit($key)) {
            return BugIncident::with('systemError')->find((int) $key);
        }

        return BugIncident::query()
            ->with('systemError')
            ->where('incident_code', strtoupper($key))
            ->orderByDesc('id')
            ->first();
    }
}
