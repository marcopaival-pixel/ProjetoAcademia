<?php

namespace App\Services;

use App\Models\DeployRelease;
use App\Support\AppVersion;
use Illuminate\Support\Facades\DB;

class DeployReleaseService
{
    public function currentVersion(): string
    {
        return AppVersion::current();
    }

    public function latestForEnvironment(string $environment): ?DeployRelease
    {
        return DeployRelease::query()
            ->where('environment', $environment)
            ->where('status', DeployRelease::STATUS_SUCCESS)
            ->orderByDesc('deployed_at')
            ->orderByDesc('id')
            ->first();
    }

    public function latestHomologPending(): ?DeployRelease
    {
        return DeployRelease::query()
            ->where('environment', DeployRelease::ENV_HOMOLOG)
            ->where('homolog_status', DeployRelease::HOMOLOG_PENDING)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function record(array $data, ?int $userId): DeployRelease
    {
        return DB::transaction(function () use ($data, $userId) {
            $environment = $data['environment'];
            $status = $data['status'] ?? DeployRelease::STATUS_SUCCESS;

            if ($status === DeployRelease::STATUS_SUCCESS) {
                DeployRelease::query()
                    ->where('environment', $environment)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }

            return DeployRelease::create([
                'version' => $data['version'] ?? AppVersion::current(),
                'environment' => $environment,
                'status' => $status,
                'homolog_status' => $data['homolog_status'] ?? null,
                'impact_level' => $data['impact_level'] ?? 'medium',
                'risk_level' => $data['risk_level'] ?? 'low',
                'deployed_by' => $userId,
                'git_branch' => $data['git_branch'] ?? null,
                'git_commit' => $data['git_commit'] ?? null,
                'notes' => $data['notes'] ?? null,
                'failure_message' => $data['failure_message'] ?? null,
                'files_changed_count' => $data['files_changed_count'] ?? null,
                'deployed_at' => $data['deployed_at'] ?? now(),
                'finished_at' => $status !== DeployRelease::STATUS_IN_PROGRESS ? now() : null,
                'is_current' => $status === DeployRelease::STATUS_SUCCESS,
            ]);
        });
    }

    public function updateHomologStatus(DeployRelease $release, string $homologStatus): DeployRelease
    {
        $release->update(['homolog_status' => $homologStatus]);

        return $release->fresh();
    }
}
