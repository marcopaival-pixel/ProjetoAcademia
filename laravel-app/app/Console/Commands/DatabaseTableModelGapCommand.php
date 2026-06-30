<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;

class DatabaseTableModelGapCommand extends Command
{
    protected $signature = 'app:db:table-model-gap
                            {--prefix= : Filtrar tabelas por prefixo (ex.: shop_)}
                            {--fail-on-gap : Exit code 1 se existir lacuna}';

    protected $description = 'Compara tabelas da BD com modelos Eloquent (lacunas AS-IS).';

    /** @var list<string> */
    private const IGNORED_TABLES = [
        'migrations',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
        'sessions',
        'personal_access_tokens',
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
        'pulse_entries',
        'pulse_values',
        'pulse_aggregates',
    ];

    public function handle(): int
    {
        $report = self::audit(
            $this->option('prefix') !== null && $this->option('prefix') !== ''
                ? (string) $this->option('prefix')
                : null
        );

        $this->info('=== Tabelas sem modelo ===');
        if ($report['tables_without_model'] === []) {
            $this->line('  (nenhuma)');
        } else {
            foreach ($report['tables_without_model'] as $table) {
                $this->warn("  - {$table}");
            }
        }

        $this->newLine();
        $this->info('=== Modelos sem tabela ===');
        if ($report['models_without_table'] === []) {
            $this->line('  (nenhum)');
        } else {
            foreach ($report['models_without_table'] as $entry) {
                $this->warn("  - {$entry['model']} → {$entry['table']}");
            }
        }

        $this->newLine();
        $this->line('Resumo: '.$report['summary']);

        if ($this->option('fail-on-gap') && $report['has_gaps']) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return array{
     *     tables_without_model: list<string>,
     *     models_without_table: list<array{model: string, table: string}>,
     *     mapped_tables: list<string>,
     *     has_gaps: bool,
     *     summary: string
     * }
     */
    public static function audit(?string $tablePrefix = null): array
    {
        $modelTables = self::discoverModelTables();
        $dbTables = self::discoverDatabaseTables($tablePrefix);

        $mappedTableNames = array_values(array_unique(array_column($modelTables, 'table')));
        $tablesWithoutModel = array_values(array_diff($dbTables, $mappedTableNames));
        sort($tablesWithoutModel);

        $modelsWithoutTable = [];
        foreach ($modelTables as $entry) {
            if (! in_array($entry['table'], $dbTables, true)) {
                $modelsWithoutTable[] = $entry;
            }
        }

        $gapCount = count($tablesWithoutModel) + count($modelsWithoutTable);

        return [
            'tables_without_model' => $tablesWithoutModel,
            'models_without_table' => $modelsWithoutTable,
            'mapped_tables' => $mappedTableNames,
            'has_gaps' => $gapCount > 0,
            'summary' => sprintf(
                '%d tabela(s) sem modelo, %d modelo(s) sem tabela (%d mapeamento(s) ativo(s))',
                count($tablesWithoutModel),
                count($modelsWithoutTable),
                count($mappedTableNames)
            ),
        ];
    }

    /**
     * @return list<array{model: string, table: string}>
     */
    private static function discoverModelTables(): array
    {
        $out = [];
        $path = app_path('Models');

        if (! is_dir($path)) {
            return $out;
        }

        foreach (File::allFiles($path) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace(['/', '\\'], '\\', $file->getRelativePathname());
            $class = 'App\\Models\\'.str_replace('.php', '', $relative);

            if (! class_exists($class)) {
                continue;
            }

            $ref = new ReflectionClass($class);
            if ($ref->isAbstract() || ! $ref->isSubclassOf(Model::class)) {
                continue;
            }

            /** @var Model $instance */
            $instance = $ref->newInstance();
            $out[] = [
                'model' => $class,
                'table' => $instance->getTable(),
            ];
        }

        usort($out, fn (array $a, array $b) => strcmp($a['table'], $b['table']));

        return $out;
    }

    /**
     * @return list<string>
     */
    private static function discoverDatabaseTables(?string $tablePrefix): array
    {
        $tables = [];

        foreach (Schema::getTables() as $meta) {
            $name = (string) ($meta['name'] ?? '');
            if ($name === '' || in_array($name, self::IGNORED_TABLES, true)) {
                continue;
            }
            if ($tablePrefix !== null && ! str_starts_with($name, $tablePrefix)) {
                continue;
            }
            $tables[] = $name;
        }

        sort($tables);

        return $tables;
    }
}
