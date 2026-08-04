<?php

namespace App\Console\Commands;

use App\Models\BugIncident;
use App\Services\BugSurgeonAgentReportService;
use Illuminate\Console\Command;

class BugSurgeonSubmitAnalysisCommand extends Command
{
    protected $signature = 'bug-surgeon:submit-analysis
                            {incident : incident_code ou id}
                            {--file= : JSON com diagnóstico, impacto e diff}
                            {--stdin : ler JSON do stdin}';

    protected $description = 'Registra relatório do agente (diagnóstico, impacto, patch) no incidente';

    public function handle(BugSurgeonAgentReportService $reports): int
    {
        $incident = $this->resolveIncident((string) $this->argument('incident'));
        if ($incident === null) {
            $this->error('Incidente não encontrado.');

            return self::FAILURE;
        }

        $json = $this->option('stdin')
            ? stream_get_contents(STDIN)
            : (is_readable((string) $this->option('file')) ? file_get_contents((string) $this->option('file')) : null);

        if (! is_string($json) || trim($json) === '') {
            $this->error('Informe --file=report.json ou --stdin');

            return self::FAILURE;
        }

        $data = json_decode($json, true);
        if (! is_array($data)) {
            $this->error('JSON inválido.');

            return self::FAILURE;
        }

        try {
            $updated = $reports->applyReport($incident, $data);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("{$updated->incident_code} → {$updated->statusLabel()} ({$updated->state})");
        if ($updated->confidence !== null) {
            $this->line("Confiança: {$updated->confidence}%");
        }

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
