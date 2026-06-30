<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupVerifyCommand extends Command
{
    protected $signature = 'app:backup:verify
                            {--path= : Pasta de backups nativos (default storage/app/backups)}
                            {--fail-on-empty : Exit 1 se encontrar ficheiro vazio}';

    protected $description = 'Verifica backups nativos (.sql) — deteta ficheiros vazios (auditoria BD).';

    public function handle(): int
    {
        $path = $this->option('path') ?: storage_path('app/backups');

        if (! is_dir($path)) {
            $this->info("Pasta de backups inexistente: {$path}");

            return self::SUCCESS;
        }

        $empty = [];
        $valid = 0;

        foreach (File::files($path) as $file) {
            if (! str_ends_with($file->getFilename(), '.sql')) {
                continue;
            }

            $size = $file->getSize();
            if ($size === 0) {
                $empty[] = $file->getFilename();
            } else {
                $valid++;
            }
        }

        if ($empty === []) {
            $this->info("Backups nativos OK — {$valid} ficheiro(s) .sql com conteúdo.");

            return self::SUCCESS;
        }

        foreach ($empty as $name) {
            $this->error("[vazio] {$name}");
        }

        $this->warn(count($empty).' backup(s) vazio(s) — verificar mysqldump (XAMPP PATH).');

        if ($this->option('fail-on-empty')) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
