<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseIndexExplainCommand extends Command
{
    protected $signature = 'app:db:index-explain {--fail-on-scan : Exit code 1 se algum plano usar ALL/full scan}';

    protected $description = 'Valida planos EXPLAIN das consultas críticas identificadas na auditoria de índices.';

    /** @var list<array{label: string, sql: string, bad_types: list<string>, preferred_keys: list<string>}> */
    private array $checks = [];

    public function handle(): int
    {
        if (! Schema::hasTable('users')) {
            $this->error('Base de dados inacessível ou migrações pendentes.');

            return self::FAILURE;
        }

        $since = now()->subDays(30)->toDateTimeString();
        $todayStart = now()->startOfDay()->toDateTimeString();
        $todayEnd = now()->endOfDay()->toDateTimeString();

        $this->checks = [
            [
                'label' => 'payments (status + created_at)',
                'sql' => "EXPLAIN SELECT COUNT(*) FROM payments WHERE status IN ('paid','approved') AND created_at >= '{$since}'",
                'bad_types' => ['ALL'],
                'preferred_keys' => ['idx_payments_status_created'],
            ],
            [
                'label' => 'financial_logs (transaction_id + action)',
                'sql' => "EXPLAIN SELECT 1 FROM financial_logs WHERE transaction_id = 'test-gateway' AND action IN ('PAYMENT_RECEIVED','AI_CREDITS_PURCHASED') LIMIT 1",
                'bad_types' => ['ALL'],
                'preferred_keys' => ['idx_finlog_tx_action'],
            ],
            [
                'label' => 'professional_appointments (prof + datetime range)',
                'sql' => "EXPLAIN SELECT id FROM professional_appointments WHERE professional_id = 1 AND appointment_at BETWEEN '{$todayStart}' AND '{$todayEnd}' AND status IN ('scheduled','confirmed')",
                'bad_types' => ['ALL'],
                'preferred_keys' => ['idx_appt_prof_datetime', 'professional_appointments_professional_id_foreign'],
            ],
            [
                'label' => 'pacientes (profissional + status)',
                'sql' => "EXPLAIN SELECT user_id FROM pacientes WHERE profissional_id = 1 AND status = 'Sim'",
                'bad_types' => ['ALL'],
                'preferred_keys' => ['idx_pacientes_prof_status', 'paciente_profissional_profissional_id_paciente_id_index'],
            ],
            [
                'label' => 'food_entries (user + entry_date)',
                'sql' => "EXPLAIN SELECT id FROM food_entries WHERE user_id = 1 AND entry_date BETWEEN '2026-01-01' AND '2026-06-01'",
                'bad_types' => ['ALL'],
                'preferred_keys' => ['food_entries_user_date', 'idx_user_food_date'],
            ],
        ];

        $failed = 0;
        $this->info('=== Validação EXPLAIN (auditoria de índices) ===');
        $this->newLine();

        foreach ($this->checks as $check) {
            if (! $this->tableExistsForCheck($check['sql'])) {
                $this->warn("  [skip] {$check['label']} — tabela ausente");
                continue;
            }

            $plan = DB::select($check['sql']);
            $row = $plan[0] ?? null;

            if ($row === null) {
                $this->error("  [falha] {$check['label']} — EXPLAIN sem resultado");
                $failed++;

                continue;
            }

            $type = (string) ($row->type ?? '');
            $key = (string) ($row->key ?? '');
            $rows = (string) ($row->rows ?? '?');
            $extra = (string) ($row->Extra ?? '');

            $isBad = in_array($type, $check['bad_types'], true);
            $usesPreferred = $key !== '' && (
                in_array($key, $check['preferred_keys'], true)
                || $this->isAcceptableKey($check['label'], $key)
            );

            if ($isBad) {
                $this->error("  [falha] {$check['label']}: type={$type}, key=".($key ?: 'null').", rows={$rows}, Extra={$extra}");
                $failed++;
            } elseif (! $usesPreferred && $check['preferred_keys'] !== []) {
                $this->warn("  [!] {$check['label']}: type={$type}, key={$key} (esperado: ".implode(' ou ', $check['preferred_keys']).")");
            } else {
                $this->line("  [ok] {$check['label']}: type={$type}, key={$key}, rows={$rows}");
            }
        }

        $this->newLine();
        $this->line('Índices duplicados (food/water):');
        $this->reportDuplicateIndex('food_entries', 'user_id,entry_date');
        $this->reportDuplicateIndex('water_entries', 'user_id,entry_date');

        if ($failed > 0 && $this->option('fail-on-scan')) {
            $this->error("{$failed} consulta(s) com full scan detectado(s).");

            return self::FAILURE;
        }

        if ($failed > 0) {
            $this->warn("{$failed} consulta(s) com full scan — execute: php artisan migrate");
        } else {
            $this->info('Planos críticos dentro do esperado.');
        }

        return self::SUCCESS;
    }

    private function tableExistsForCheck(string $sql): bool
    {
        if (preg_match('/FROM\s+([a-z0-9_]+)/i', $sql, $m)) {
            return Schema::hasTable($m[1]);
        }

        return true;
    }

    private function isAcceptableKey(string $label, string $key): bool
    {
        return str_contains($label, 'food_entries') && str_contains($key, 'user');
    }

    private function reportDuplicateIndex(string $table, string $columnSignature): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $indexes = $connection->select(
            'SELECT INDEX_NAME, GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS cols
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME <> ?
             GROUP BY INDEX_NAME
             HAVING cols = ?',
            [$database, $table, 'PRIMARY', $columnSignature]
        );

        if (count($indexes) > 1) {
            $names = collect($indexes)->pluck('INDEX_NAME')->implode(', ');
            $this->warn("  [!] {$table}: índices duplicados em ({$columnSignature}): {$names}");
            $this->line('      → aplicar migração 2026_06_24_120001 após validação.');
        } elseif (count($indexes) === 1) {
            $this->line("  [ok] {$table}: um índice em ({$columnSignature})");
        }
    }
}
