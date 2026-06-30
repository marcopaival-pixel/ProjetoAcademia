<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class NativeDatabaseBackupCommand extends Command
{
    protected $signature = 'app:backup:native
                            {--path= : Caminho completo do ficheiro .sql (opcional)}';

    protected $description = 'Gera backup nativo MySQL via mysqldump (XAMPP/produção).';

    public function handle(): int
    {
        $dbConfig = config('database.connections.mysql');
        $fileName = 'native_db_backup_'.now()->format('Y-m-d_H-i-s').'.sql';
        $storagePath = $this->option('path') ?: storage_path('app/backups/'.$fileName);

        File::ensureDirectoryExists(dirname($storagePath));

        $mysqldump = $this->resolveMysqldumpPath();
        $passwordPart = $dbConfig['password'] ? '--password='.escapeshellarg($dbConfig['password']) : '';

        $command = sprintf(
            '%s --user=%s %s --host=%s --result-file=%s %s',
            escapeshellarg($mysqldump),
            escapeshellarg($dbConfig['username']),
            $passwordPart,
            escapeshellarg($dbConfig['host']),
            escapeshellarg($storagePath),
            escapeshellarg($dbConfig['database'])
        );

        $this->info('A gerar backup nativo...');

        $result = Process::timeout(600)->run($command);

        if (! $result->successful()) {
            $this->error($result->errorOutput() ?: 'mysqldump falhou com código '.$result->exitCode());

            return self::FAILURE;
        }

        if (! file_exists($storagePath) || filesize($storagePath) === 0) {
            if (file_exists($storagePath)) {
                @unlink($storagePath);
            }
            $this->error('Backup gerado vazio — verificar mysqldump no PATH (XAMPP) e credenciais MySQL.');

            return self::FAILURE;
        }

        $sizeMb = round(filesize($storagePath) / 1024 / 1024, 2);
        $this->info("Backup OK: {$storagePath} ({$sizeMb} MB)");

        return self::SUCCESS;
    }

    private function resolveMysqldumpPath(): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $xampp = 'C:\xampp\mysql\bin\mysqldump.exe';
            if (file_exists($xampp)) {
                return $xampp;
            }
        }

        return 'mysqldump';
    }
}
