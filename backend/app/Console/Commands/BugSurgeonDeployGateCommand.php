<?php

namespace App\Console\Commands;

use App\Models\BugIncident;
use App\Services\BugSurgeonDeployGateService;
use Illuminate\Console\Command;

class BugSurgeonDeployGateCommand extends Command
{
    protected $signature = 'bug-surgeon:deploy-gate
                            {--target=homologacao : homologacao ou production}
                            {--branch= : branch git (default: BUGFIX_BRANCH env)}';

    protected $description = 'Valida aprovações Bug Surgeon antes de deploy em branch bugfix/*';

    public function handle(BugSurgeonDeployGateService $gate): int
    {
        $branch = $this->option('branch') ?: getenv('BUGFIX_BRANCH') ?: $this->detectGitBranch();
        $target = (string) $this->option('target');

        $this->info('Bug Surgeon deploy gate');
        $this->line('Branch: ' . ($branch ?: '(não detectada)'));
        $this->line('Alvo: ' . $target);

        if (! $gate->isBugfixBranch($branch)) {
            $this->comment('Branch não é bugfix/* — gate ignorado.');

            return self::SUCCESS;
        }

        $errors = $gate->validateDeploy($target, $branch);
        if ($errors !== []) {
            foreach ($errors as $error) {
                $this->error('  [bloqueado] ' . $error);
            }

            return self::FAILURE;
        }

        $incident = $gate->findIncidentForBranch($branch);
        $this->info('  [ok] ' . ($incident?->incident_code ?? 'incidente') . ' autorizado para ' . $target);

        return self::SUCCESS;
    }

    private function detectGitBranch(): ?string
    {
        $head = base_path('../.git/HEAD');
        if (! is_file($head)) {
            $head = base_path('.git/HEAD');
        }
        if (! is_file($head)) {
            return null;
        }

        $ref = trim((string) file_get_contents($head));
        if (str_starts_with($ref, 'ref: ')) {
            return basename(trim(substr($ref, 5)));
        }

        return null;
    }
}
