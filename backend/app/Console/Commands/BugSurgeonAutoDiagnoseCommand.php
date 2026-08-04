<?php

namespace App\Console\Commands;

use App\Models\BugIncident;
use App\Services\BugSurgeonAutoDiagnosisService;
use Illuminate\Console\Command;

class BugSurgeonAutoDiagnoseCommand extends Command
{
    protected $signature = 'bug-surgeon:auto-diagnose
                            {incident : incident_code ou id}';

    protected $description = 'Executa diagnóstico automático via IA (context pack → WAITING_APPROVAL)';

    public function handle(BugSurgeonAutoDiagnosisService $diagnosis): int
    {
        $incident = $this->resolveIncident((string) $this->argument('incident'));
        if ($incident === null) {
            $this->error('Incidente não encontrado.');

            return self::FAILURE;
        }

        try {
            $updated = $diagnosis->run($incident);
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
            return BugIncident::with('systemError')->find((int) $key);
        }

        return BugIncident::query()
            ->with('systemError')
            ->where('incident_code', strtoupper($key))
            ->orderByDesc('id')
            ->first();
    }
}
