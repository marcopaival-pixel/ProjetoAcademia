<?php

namespace Database\Seeders;

use App\Models\DeployRelease;
use App\Support\AppVersion;
use Illuminate\Database\Seeder;

/**
 * Registra release inicial de homologação aprovada para a versão atual (bootstrap de deploy).
 */
class DeployHomologBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        $version = AppVersion::current();

        $exists = DeployRelease::query()
            ->where('version', $version)
            ->where('environment', DeployRelease::ENV_HOMOLOG)
            ->where('homolog_status', DeployRelease::HOMOLOG_APPROVED)
            ->exists();

        if ($exists) {
            return;
        }

        DeployRelease::create([
            'version' => $version,
            'environment' => DeployRelease::ENV_HOMOLOG,
            'status' => DeployRelease::STATUS_SUCCESS,
            'homolog_status' => DeployRelease::HOMOLOG_APPROVED,
            'impact_level' => 'low',
            'risk_level' => 'low',
            'git_branch' => 'homologacao',
            'notes' => 'Bootstrap automático — homologação aprovada para checklist de produção.',
            'deployed_at' => now(),
            'finished_at' => now(),
            'is_current' => true,
        ]);
    }
}
