<?php

namespace App\Console\Commands;

use App\Models\BugIncident;
use App\Services\BugIncidentService;
use Illuminate\Console\Command;

class BugSurgeonCiPassedCommand extends Command
{
    protected $signature = 'bug-surgeon:ci-passed
                            {incident : incident_code ou id}
                            {--branch= : validar branch bugfix vinculada}';

    protected $description = 'Marca testes CI como aprovados (TESTING → READY_FOR_STAGING)';

    public function handle(BugIncidentService $incidents): int
    {
        $incident = $this->resolveIncident((string) $this->argument('incident'));
        if ($incident === null) {
            $this->error('Incidente não encontrado.');

            return self::FAILURE;
        }

        $branch = $this->option('branch');
        if ($branch && $incident->branch_name && ! str_contains(strtolower($branch), strtolower($incident->incident_code))) {
            $this->warn("Branch informada ({$branch}) difere do incidente ({$incident->branch_name}).");
        }

        try {
            $updated = $incidents->markCiPassed($incident);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("{$updated->incident_code} → {$updated->state}");

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
