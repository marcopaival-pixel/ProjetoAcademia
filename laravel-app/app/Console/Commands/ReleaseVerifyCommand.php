<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ReleaseVerifyCommand extends Command
{
    protected $signature = 'app:release:verify {--target=homologacao : homologacao ou production} {--with-tests : Executa php artisan test após verificações}';

    protected $description = 'Verificação completa pré-release: tenant, checklist, smoke e (opcional) PHPUnit.';

    public function handle(): int
    {
        $target = (string) $this->option('target');
        $failed = 0;

        $this->info('=== Verificação de release ===');
        $this->newLine();

        $steps = [
            'Isolamento multi-tenant' => fn () => Artisan::call('app:audit:tenant'),
            'Checklist pré-deploy' => fn () => Artisan::call('app:deploy:checklist', ['--target' => $target]),
            'Smoke test' => fn () => Artisan::call('app:smoke:test', ['--target' => $target]),
        ];

        foreach ($steps as $label => $runner) {
            $this->comment("→ {$label}");
            $exit = $runner();
            $output = trim(Artisan::output());
            if ($output !== '') {
                $this->line($output);
            }
            if ($exit !== 0) {
                $failed++;
            }
            $this->newLine();
        }

        if ($this->option('with-tests')) {
            $this->comment('→ PHPUnit');
            $testExit = Artisan::call('test');
            $testOutput = trim(Artisan::output());
            if ($testOutput !== '') {
                $this->line($testOutput);
            }
            if ($testExit !== 0) {
                $failed++;
            }
            $this->newLine();
        } else {
            $this->comment('Dica: execute `php artisan app:release:verify --with-tests` antes do go-live.');
        }

        if ($failed > 0) {
            $this->error("Verificação de release: {$failed} etapa(s) com falha.");

            return self::FAILURE;
        }

        $this->info('Verificação de release concluída com sucesso.');

        if ($target === 'production') {
            $this->line('Próximo passo: aprovar homologação em /admin/deploy e smoke manual (checkout MP sandbox + webhook).');
        }

        return self::SUCCESS;
    }
}
