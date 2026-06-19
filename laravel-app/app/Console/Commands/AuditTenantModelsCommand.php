<?php

namespace App\Console\Commands;

use App\Services\TenantIsolationAuditor;
use Illuminate\Console\Command;

class AuditTenantModelsCommand extends Command
{
    protected $signature = 'app:audit:tenant {--json : Saída JSON}';

    protected $description = 'Lista models sem trait de isolamento multiempresa (risco de vazamento).';

    public function handle(TenantIsolationAuditor $auditor): int
    {
        $result = $auditor->audit();

        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return empty($result['missing']) ? self::SUCCESS : self::FAILURE;
        }

        $this->info('Models com isolamento (traits): ' . count($result['isolated']));
        $this->info('Models globais (allowlist): ' . count($result['global_ok']));
        $this->newLine();

        if ($result['missing'] === []) {
            $this->info('Nenhum model suspeito fora da allowlist.');

            return self::SUCCESS;
        }

        $this->warn('Models SEM trait de tenant (revisar):');
        $rows = [];
        foreach ($result['missing'] as $item) {
            $cols = $item['columns'] !== [] ? implode(', ', $item['columns']) : '—';
            $risk = $item['has_db_columns'] ? 'ALTO (tem colunas tenant)' : 'médio';
            $rows[] = [class_basename($item['class']), $item['table'], $cols, $risk];
        }

        $this->table(['Model', 'Tabela', 'Colunas DB', 'Risco'], $rows);

        $high = array_filter($result['missing'], fn ($m) => $m['has_db_columns']);

        return $high !== [] ? self::FAILURE : self::SUCCESS;
    }
}
