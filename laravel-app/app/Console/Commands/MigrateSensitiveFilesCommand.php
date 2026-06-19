<?php

namespace App\Console\Commands;

use App\Services\SecureFileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateSensitiveFilesCommand extends Command
{
    protected $signature = 'files:migrate-sensitive {--dry-run : Simular sem mover ficheiros}';

    protected $description = 'Move ficheiros sensíveis (fotos de evolução, análise corporal, galeria, importações) do disco público para o privado.';

    /**
     * Diretórios com dados sensíveis que devem residir no disco privado.
     *
     * @var list<string>
     */
    private const SENSITIVE_DIRECTORIES = [
        'evolution',
        'body-analyses',
        'gallery_photos',
        'workout_imports',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $publicDisk = Storage::disk('public');
        $privateDisk = Storage::disk(SecureFileService::PRIVATE_DISK);

        if ($dryRun) {
            $this->warn('Modo dry-run — nenhum ficheiro será movido.');
        }

        $moved = 0;
        $skipped = 0;
        $missing = 0;

        foreach (self::SENSITIVE_DIRECTORIES as $directory) {
            if (! $publicDisk->exists($directory)) {
                $this->line("Diretório público inexistente (ignorado): {$directory}");
                continue;
            }

            foreach ($publicDisk->allFiles($directory) as $path) {
                if (str_ends_with($path, '.htaccess')) {
                    continue;
                }

                if ($privateDisk->exists($path)) {
                    $skipped++;
                    continue;
                }

                $contents = $publicDisk->get($path);
                if ($contents === null) {
                    $missing++;
                    continue;
                }

                if (! $dryRun) {
                    $privateDisk->put($path, $contents);
                    $publicDisk->delete($path);
                }

                $moved++;
                $this->line(($dryRun ? '[dry] ' : '').'Movido: '.$path);
            }
        }

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Ficheiros movidos', $moved],
                ['Já existiam no privado (ignorados)', $skipped],
                ['Ilegíveis', $missing],
            ]
        );

        $this->info($dryRun ? 'Simulação concluída.' : 'Migração concluída.');

        return self::SUCCESS;
    }
}
