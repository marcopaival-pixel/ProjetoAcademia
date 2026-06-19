<?php

namespace App\Console\Commands;

use App\Support\AppVersion;
use Illuminate\Console\Command;

class AppVersionCommand extends Command
{
    protected $signature = 'app:version
                            {--patch : Incrementa PATCH (correções)}
                            {--minor : Incrementa MINOR (novas funcionalidades)}
                            {--major : Incrementa MAJOR (breaking changes)}
                            {--note= : Texto para entrada no CHANGELOG}
                            {--no-changelog : Não atualiza CHANGELOG.md}';

    protected $description = 'Exibe ou atualiza a versão semver da aplicação (arquivo VERSION).';

    public function handle(): int
    {
        if ($this->option('patch')) {
            return $this->bump('patch');
        }

        if ($this->option('minor')) {
            return $this->bump('minor');
        }

        if ($this->option('major')) {
            return $this->bump('major');
        }

        $this->line('<info>' . AppVersion::display() . '</info>');
        $this->line('Laravel ' . app()->version());
        $this->line('PHP ' . PHP_VERSION);
        $this->line('Ambiente ' . config('app.env'));

        return self::SUCCESS;
    }

    private function bump(string $part): int
    {
        $previous = AppVersion::current();
        $next = AppVersion::bump($part);

        if (! $this->option('no-changelog')) {
            AppVersion::appendChangelogSection($next, (string) $this->option('note'));
        }

        $this->info("Versão atualizada: v{$previous} → v{$next}");

        return self::SUCCESS;
    }
}
