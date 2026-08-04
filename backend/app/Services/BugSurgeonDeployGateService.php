<?php

namespace App\Services;

use App\Models\BugIncident;
use RuntimeException;

class BugSurgeonDeployGateService
{
    public function extractIncidentCodeFromBranch(?string $branch): ?string
    {
        if ($branch === null || $branch === '') {
            return null;
        }

        if (preg_match('/bugfix\/(bug-\d{4}-\d{5})/i', $branch, $matches) !== 1) {
            return null;
        }

        return strtoupper($matches[1]);
    }

    public function isBugfixBranch(?string $branch): bool
    {
        return $this->extractIncidentCodeFromBranch($branch) !== null;
    }

    public function findIncidentForBranch(?string $branch): ?BugIncident
    {
        $code = $this->extractIncidentCodeFromBranch($branch);
        if ($code === null) {
            return null;
        }

        return BugIncident::query()
            ->where('incident_code', $code)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @return list<string> validation errors (empty = OK)
     */
    public function validateDeploy(string $target, ?string $branch): array
    {
        if (! $this->isBugfixBranch($branch)) {
            return [];
        }

        $incident = $this->findIncidentForBranch($branch);
        if ($incident === null) {
            return ["Branch bugfix sem incidente registrado no admin: {$branch}"];
        }

        $errors = [];

        if (! $incident->approval_patch) {
            $errors[] = "{$incident->incident_code}: correção não aprovada no Bug Surgeon.";
        }

        if (! filled($incident->commit_hash)) {
            $errors[] = "{$incident->incident_code}: commit da correção não registrado.";
        }

        $target = $this->normalizeTarget($target);

        if ($target === 'homologacao') {
            if (! $incident->approval_staging) {
                $errors[] = "{$incident->incident_code}: staging não aprovado no admin.";
            }

            if (! in_array($incident->state, [
                BugIncident::STATE_READY_FOR_STAGING,
                BugIncident::STATE_WAITING_DEPLOY_APPROVAL,
                BugIncident::STATE_DEPLOYED,
                BugIncident::STATE_MONITORING,
            ], true)) {
                $errors[] = "{$incident->incident_code}: estado {$incident->state} não permite deploy em homologação (esperado READY_FOR_STAGING+).";
            }
        }

        if ($target === 'production') {
            if (! $incident->approval_staging) {
                $errors[] = "{$incident->incident_code}: staging não aprovado.";
            }

            if (! $incident->approval_production) {
                $errors[] = "{$incident->incident_code}: produção não aprovada no admin.";
            }

            if ($incident->state === BugIncident::STATE_ROLLED_BACK) {
                $errors[] = "{$incident->incident_code}: incidente em ROLLED_BACK — nova autorização necessária.";
            }
        }

        return $errors;
    }

    public function assertDeployAllowed(string $target, ?string $branch): void
    {
        $errors = $this->validateDeploy($target, $branch);
        if ($errors !== []) {
            throw new RuntimeException(implode(' ', $errors));
        }
    }

    /** @return list<string> */
    public function resolveTestFilters(?string $branch): array
    {
        $planPath = base_path('../.cursor/bug-surgeon/test-plan.json');
        if (! is_file($planPath)) {
            $planPath = base_path('.cursor/bug-surgeon/test-plan.json');
        }

        if (is_file($planPath)) {
            $plan = json_decode((string) file_get_contents($planPath), true);
            if (is_array($plan) && ! empty($plan['tests']) && is_array($plan['tests'])) {
                return array_values(array_filter(array_map('strval', $plan['tests'])));
            }
        }

        $incident = $this->findIncidentForBranch($branch);
        if ($incident !== null) {
            return ['BugIncidentServiceTest'];
        }

        return [];
    }

    private function normalizeTarget(string $target): string
    {
        return match (strtolower($target)) {
            'staging', 'homolog', 'homologacao' => 'homologacao',
            'prod', 'production' => 'production',
            default => strtolower($target),
        };
    }
}
