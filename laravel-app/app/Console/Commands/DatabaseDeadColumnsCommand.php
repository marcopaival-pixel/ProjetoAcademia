<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseDeadColumnsCommand extends Command
{
    protected $signature = 'app:db:dead-columns
                            {--table= : Auditar apenas uma tabela}
                            {--limit=40 : Máximo de tabelas com model}
                            {--min-confidence=70 : Confiança mínima para listar (inferência)}';

    protected $description = 'Heurística de colunas possivelmente mortas (BD ↔ model ↔ código) — inferência, não prova absoluta.';

    /** @var list<string> */
    private const SKIP_COLUMNS = [
        'id', 'created_at', 'updated_at', 'deleted_at',
        'password', 'remember_token', 'email_verified_at',
    ];

    /** @var list<string> */
    private const GENERIC_COLUMNS = [
        'status', 'name', 'type', 'title', 'description', 'notes', 'email', 'slug',
        'code', 'value', 'amount', 'data', 'payload', 'settings', 'config', 'meta',
        'options', 'is_active', 'active', 'sort', 'order', 'position', 'level', 'rate',
        'total', 'count', 'user_id', 'plan_id', 'clinic_id',
    ];

    public function handle(): int
    {
        if (! Schema::hasTable('users')) {
            $this->error('Base de dados inacessível ou migrações pendentes.');

            return self::FAILURE;
        }

        $tableFilter = $this->option('table');
        $limit = max(1, (int) $this->option('limit'));
        $minConfidence = max(0, min(100, (int) $this->option('min-confidence')));

        $modelsByTable = $this->discoverModelsByTable();
        if ($tableFilter) {
            if (! isset($modelsByTable[$tableFilter]) && ! Schema::hasTable($tableFilter)) {
                $this->error("Tabela desconhecida ou sem model: {$tableFilter}");

                return self::FAILURE;
            }
            $modelsByTable = isset($modelsByTable[$tableFilter])
                ? [$tableFilter => $modelsByTable[$tableFilter]]
                : [];
        }

        ksort($modelsByTable);
        $modelsByTable = array_slice($modelsByTable, 0, $limit, true);

        $this->info('=== Colunas possivelmente mortas (heurística — inferência) ===');
        $this->warn('Busca estática; nomes genéricos são ignorados. Validar manualmente antes de remover.');
        $this->newLine();

        $searchRoots = array_filter([
            app_path(),
            resource_path('views'),
            database_path('factories'),
        ], is_dir(...));

        $findings = [];

        foreach ($modelsByTable as $table => $modelClass) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            /** @var Model $model */
            $model = new $modelClass;
            $fillable = array_flip($model->getFillable());
            $casts = array_flip(array_keys($model->getCasts()));
            $hidden = array_flip($model->getHidden());

            foreach (Schema::getColumnListing($table) as $column) {
                if (in_array($column, self::SKIP_COLUMNS, true)) {
                    continue;
                }

                if (in_array($column, self::GENERIC_COLUMNS, true) && ! str_contains($column, '_')) {
                    continue;
                }

                if (isset($fillable[$column]) || isset($casts[$column]) || isset($hidden[$column])) {
                    continue;
                }

                $refs = $this->countCodeReferences($column, $searchRoots, $modelClass);
                $confidence = $this->estimateConfidence($column, $refs);

                if ($confidence < $minConfidence) {
                    continue;
                }

                $findings[] = [
                    'table' => $table,
                    'column' => $column,
                    'model' => $modelClass,
                    'refs' => $refs,
                    'confidence' => $confidence,
                ];
            }
        }

        if ($findings === []) {
            $this->info('Nenhuma coluna suspeita encontrada com os critérios atuais.');

            return self::SUCCESS;
        }

        usort($findings, fn (array $a, array $b) => $b['confidence'] <=> $a['confidence']);

        $this->table(
            ['Tabela', 'Coluna', 'Model', 'Refs código', 'Confiança %'],
            array_map(fn (array $f) => [
                $f['table'],
                $f['column'],
                class_basename($f['model']),
                $f['refs'],
                $f['confidence'],
            ], $findings)
        );

        $this->newLine();
        $this->line('Total suspeitas: '.count($findings).' — marcar achados como inferência na auditoria.');

        return self::SUCCESS;
    }

    /**
     * @return array<string, class-string<Model>>
     */
    private function discoverModelsByTable(): array
    {
        $map = [];
        $paths = array_merge(
            glob(app_path('Models/*.php')) ?: [],
            glob(app_path('Models/*/*.php')) ?: []
        );

        foreach ($paths as $path) {
            $relative = str_replace([app_path('Models').DIRECTORY_SEPARATOR, '.php'], ['', ''], $path);
            $class = 'App\\Models\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);

            if (! class_exists($class)) {
                continue;
            }

            try {
                $instance = new $class;
            } catch (\Throwable) {
                continue;
            }

            if (! $instance instanceof Model) {
                continue;
            }

            $map[$instance->getTable()] = $class;
        }

        return $map;
    }

    /**
     * @param  list<string>  $searchRoots
     * @param  class-string<Model>  $modelClass
     */
    private function countCodeReferences(string $column, array $searchRoots, string $modelClass): int
    {
        $hits = 0;
        $needle = preg_quote($column, '/');
        $pattern = '/\b'.$needle.'\b/';

        foreach ($searchRoots as $root) {
            foreach (File::allFiles($root) as $file) {
                $path = $file->getPathname();
                if (! str_ends_with($path, '.php')) {
                    continue;
                }

                if (str_contains($path, 'database'.DIRECTORY_SEPARATOR.'migrations')) {
                    continue;
                }

                $content = File::get($path);
                if (! preg_match($pattern, $content)) {
                    continue;
                }

                $hits++;
            }
        }

        return $hits;
    }

    private function estimateConfidence(string $column, int $refs): int
    {
        if ($refs === 0) {
            return str_contains($column, '_') ? 80 : 72;
        }

        if ($refs === 1) {
            return 65;
        }

        return 40;
    }
}
