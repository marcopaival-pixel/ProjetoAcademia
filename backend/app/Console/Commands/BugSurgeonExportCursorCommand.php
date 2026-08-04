<?php

namespace App\Console\Commands;

use App\Models\BugIncident;
use App\Services\BugAgentCursorBridgeService;
use App\Services\BugIncidentService;
use Illuminate\Console\Command;

class BugSurgeonExportCursorCommand extends Command
{
    protected $signature = 'bug-surgeon:export-cursor
                            {incident : incident_code (ex: BUG-2026-00001) ou id numérico}
                            {--path= : diretório destino (default: ../.cursor/bug-surgeon)}';

    protected $description = 'Exporta session.json e authorization.json para o Cursor';

    public function handle(BugIncidentService $incidents, BugAgentCursorBridgeService $bridge): int
    {
        $incident = $this->resolveIncident((string) $this->argument('incident'));
        if ($incident === null) {
            $this->error('Incidente não encontrado.');

            return self::FAILURE;
        }

        $dest = $this->option('path') ?: $bridge->defaultExportPath();
        $authorization = ($incident->approval_patch && $incident->authorization)
            ? $incident->authorization
            : null;

        $bridge->exportToDirectory(
            $incident,
            $incidents->buildSessionExport($incident),
            $authorization,
            $dest
        );

        $this->info("  session.json → {$dest}".DIRECTORY_SEPARATOR.'session.json');
        if ($authorization !== null) {
            $this->line("  authorization.json → {$dest}".DIRECTORY_SEPARATOR.'authorization.json');
        } else {
            $this->warn('  authorization.json omitido (correção ainda não aprovada).');
        }
        $this->line("  cursor-commands.json → {$dest}".DIRECTORY_SEPARATOR.'cursor-commands.json');
        $this->info("Export concluído para {$incident->incident_code}.");

        return self::SUCCESS;
    }

    private function resolveIncident(string $key): ?BugIncident
    {
        if (ctype_digit($key)) {
            return BugIncident::find((int) $key);
        }

        return BugIncident::query()
            ->where('incident_code', strtoupper($key))
            ->orderByDesc('id')
            ->first();
    }
}
