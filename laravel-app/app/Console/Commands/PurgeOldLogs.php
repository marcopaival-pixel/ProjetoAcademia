<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurgeOldLogs extends Command
{
    protected $signature = 'app:purge-old-logs {--days= : Sobrescreve retenção global (dias)} {--force : Executar sem confirmação}';

    protected $description = 'Remove registros antigos de tabelas de log para economizar espaço e melhorar performance.';

    public function handle(): int
    {
        $globalDays = $this->option('days') !== null ? (int) $this->option('days') : null;
        $force = (bool) $this->option('force');

        $tables = [
            'admin_logs' => config('observability.retention_days.admin_logs', 30),
            'log_envio_email' => config('observability.retention_days.log_envio_email', 30),
            'system_errors' => config('observability.retention_days.system_errors', 15),
            'pdf_generation_logs' => config('observability.retention_days.pdf_generation_logs', 30),
            'api_integration_logs' => config('observability.retention_days.api_integration_logs', 30),
            'api_access_logs' => config('observability.retention_days.api_access_logs', 30),
            'auth_audit_logs' => config('observability.retention_days.auth_audit_logs', 90),
            'client_error_logs' => config('observability.retention_days.client_error_logs', 15),
            'omnichannel_logs' => 30,
            'financial_logs' => config('observability.retention_days.financial_logs', 365),
            'audit_logs' => config('observability.retention_days.audit_logs', 180),
            'representative_audits' => config('observability.retention_days.representative_audits', 365),
            'menu_permission_audit_logs' => config('observability.retention_days.menu_permission_audit_logs', 180),
            'pdf_signature_audit_logs' => config('observability.retention_days.pdf_signature_audit_logs', 365),
            'admin_clinic_access_logs' => config('observability.retention_days.admin_clinic_access_logs', 180),
        ];

        if (! $force && ! $this->confirm('Deseja remover logs antigos conforme política de retenção?')) {
            $this->info('Operação cancelada.');

            return self::SUCCESS;
        }

        foreach ($tables as $table => $defaultDays) {
            if (! Schema::hasTable($table)) {
                $this->warn("Tabela não encontrada: {$table}. Pulando.");

                continue;
            }

            $days = $globalDays ?? (int) $defaultDays;
            if ($days < 1) {
                continue;
            }

            $cutoffDate = now()->subDays($days)->format('Y-m-d H:i:s');
            $column = $table === 'log_envio_email' ? 'data_envio' : 'created_at';

            if (! Schema::hasColumn($table, $column)) {
                $column = 'created_at';
            }

            $this->info("Limpando {$table} (> {$days} dias)...");

            try {
                $count = DB::table($table)->where($column, '<', $cutoffDate)->delete();
                $this->info("Removidos {$count} registros de {$table}.");
            } catch (\Throwable $e) {
                $this->error("Erro ao limpar {$table}: ".$e->getMessage());
            }
        }

        $this->info('Limpeza de logs concluída.');

        return self::SUCCESS;
    }
}
