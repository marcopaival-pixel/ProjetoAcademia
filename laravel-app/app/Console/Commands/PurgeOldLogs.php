<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurgeOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-old-logs {--days=30 : Quantidade de dias de logs a manter} {--force : Executar sem confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove registros antigos de tabelas de log para economizar espaço e melhorar performance.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $force = (bool) $this->option('force');

        if ($days < 1) {
            $this->error('A quantidade de dias deve ser pelo menos 1.');
            return 1;
        }

        if (! $force && ! $this->confirm("Deseja realmente remover logs com mais de {$days} dias?")) {
            $this->info('Operação cancelada.');
            return 0;
        }

        $cutoffDate = now()->subDays($days)->format('Y-m-d H:i:s');
        
        $tables = [
            'admin_logs'           => 'created_at',
            'log_envio_email'      => 'created_at',
            'system_errors'        => 'created_at',
            'pdf_generation_logs'  => 'created_at',
            'api_integration_logs' => 'created_at',
            'omnichannel_logs'     => 'created_at', // Tabela comum mas verificar se existe
        ];

        foreach ($tables as $table => $column) {
            if (Schema::hasTable($table)) {
                $this->info("Limpando tabela: {$table}...");
                
                try {
                    $count = DB::table($table)
                        ->where($column, '<', $cutoffDate)
                        ->delete();
                    
                    $this->info("Removidos {$count} registros de {$table}.");
                } catch (\Throwable $e) {
                    $this->error("Erro ao limpar {$table}: " . $e->getMessage());
                }
            } else {
                $this->warn("Tabela não encontrada: {$table}. Pulando.");
            }
        }

        $this->info('Limpeza de logs concluída.');
        return 0;
    }
}
