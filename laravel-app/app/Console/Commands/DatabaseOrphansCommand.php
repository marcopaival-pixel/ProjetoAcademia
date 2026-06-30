<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseOrphansCommand extends Command
{
    protected $signature = 'app:db:orphans
                            {--check= : Executar apenas um check (ex.: appointments_patient)}
                            {--fail-on-orphans : Exit code 1 se existir algum órfão}';

    protected $description = 'Conta registos órfãos em FKs críticas (read-only — auditoria de integridade).';

    /**
     * @return list<array{key: string, label: string, child_table: string, child_column: string, parent_table: string, parent_column: string, nullable: bool}>
     */
    public static function definedChecks(): array
    {
        return [
            [
                'key' => 'appointments_patient',
                'label' => 'professional_appointments.patient_id → users.id',
                'child_table' => 'professional_appointments',
                'child_column' => 'patient_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'appointments_professional',
                'label' => 'professional_appointments.professional_id → users.id',
                'child_table' => 'professional_appointments',
                'child_column' => 'professional_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'payments_user',
                'label' => 'payments.user_id → users.id',
                'child_table' => 'payments',
                'child_column' => 'user_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'payments_subscription',
                'label' => 'payments.subscription_id → subscriptions.id (quando preenchido)',
                'child_table' => 'payments',
                'child_column' => 'subscription_id',
                'parent_table' => 'subscriptions',
                'parent_column' => 'id',
                'nullable' => true,
            ],
            [
                'key' => 'commissions_payment',
                'label' => 'commissions.payment_id → payments.id',
                'child_table' => 'commissions',
                'child_column' => 'payment_id',
                'parent_table' => 'payments',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'commissions_representative',
                'label' => 'commissions.representative_id → users.id',
                'child_table' => 'commissions',
                'child_column' => 'representative_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'shop_order_items_order',
                'label' => 'shop_order_items.order_id → shop_orders.id',
                'child_table' => 'shop_order_items',
                'child_column' => 'order_id',
                'parent_table' => 'shop_orders',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'shop_orders_user',
                'label' => 'shop_orders.user_id → users.id',
                'child_table' => 'shop_orders',
                'child_column' => 'user_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'shop_cart_items_cart',
                'label' => 'shop_cart_items.cart_id → shop_carts.id',
                'child_table' => 'shop_cart_items',
                'child_column' => 'cart_id',
                'parent_table' => 'shop_carts',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'body_assessments_user',
                'label' => 'body_assessments.user_id → users.id',
                'child_table' => 'body_assessments',
                'child_column' => 'user_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'commissions_user',
                'label' => 'commissions.user_id → users.id',
                'child_table' => 'commissions',
                'child_column' => 'user_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'referral_codes_clinic',
                'label' => 'referral_codes.clinic_id → clinics.id (quando preenchido)',
                'child_table' => 'referral_codes',
                'child_column' => 'clinic_id',
                'parent_table' => 'clinics',
                'parent_column' => 'id',
                'nullable' => true,
            ],
            [
                'key' => 'shop_products_vendor',
                'label' => 'shop_products.vendor_id → shop_vendors.id',
                'child_table' => 'shop_products',
                'child_column' => 'vendor_id',
                'parent_table' => 'shop_vendors',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'shop_products_supplier',
                'label' => 'shop_products.supplier_id → shop_suppliers.id (quando preenchido)',
                'child_table' => 'shop_products',
                'child_column' => 'supplier_id',
                'parent_table' => 'shop_suppliers',
                'parent_column' => 'id',
                'nullable' => true,
            ],
            [
                'key' => 'shop_order_items_product',
                'label' => 'shop_order_items.product_id → shop_products.id',
                'child_table' => 'shop_order_items',
                'child_column' => 'product_id',
                'parent_table' => 'shop_products',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'shop_coupon_usages_order',
                'label' => 'shop_coupon_usages.order_id → shop_orders.id',
                'child_table' => 'shop_coupon_usages',
                'child_column' => 'order_id',
                'parent_table' => 'shop_orders',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'shop_points_wallets_user',
                'label' => 'shop_points_wallets.user_id → users.id',
                'child_table' => 'shop_points_wallets',
                'child_column' => 'user_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => false,
            ],
            [
                'key' => 'financial_logs_user',
                'label' => 'financial_logs.user_id → users.id (quando preenchido)',
                'child_table' => 'financial_logs',
                'child_column' => 'user_id',
                'parent_table' => 'users',
                'parent_column' => 'id',
                'nullable' => true,
            ],
        ];
    }

    /**
     * @return array{
     *     results: list<array{key: string, label: string, count: int, status: string}>,
     *     total_orphans: int,
     *     failed_checks: int,
     *     skipped: int
     * }
     */
    public static function audit(?string $checkKey = null): array
    {
        $checks = self::definedChecks();

        if ($checkKey !== null && $checkKey !== '') {
            $checks = array_values(array_filter($checks, fn (array $c) => $c['key'] === $checkKey));
        }

        $results = [];
        $totalOrphans = 0;
        $failedChecks = 0;
        $skipped = 0;

        foreach ($checks as $check) {
            if (! Schema::hasTable($check['child_table']) || ! Schema::hasTable($check['parent_table'])) {
                $results[] = ['key' => $check['key'], 'label' => $check['label'], 'count' => 0, 'status' => 'skipped'];
                $skipped++;

                continue;
            }

            if (! Schema::hasColumn($check['child_table'], $check['child_column'])) {
                $results[] = ['key' => $check['key'], 'label' => $check['label'], 'count' => 0, 'status' => 'skipped'];
                $skipped++;

                continue;
            }

            $count = self::countOrphansForCheck($check);
            $status = $count > 0 ? 'orphans' : 'ok';
            $results[] = ['key' => $check['key'], 'label' => $check['label'], 'count' => $count, 'status' => $status];

            if ($count > 0) {
                $totalOrphans += $count;
                $failedChecks++;
            }
        }

        return [
            'results' => $results,
            'total_orphans' => $totalOrphans,
            'failed_checks' => $failedChecks,
            'skipped' => $skipped,
        ];
    }

    public function handle(): int
    {
        if (! Schema::hasTable('users')) {
            $this->error('Base de dados inacessível ou migrações pendentes.');

            return self::FAILURE;
        }

        $filter = $this->option('check');
        if ($filter !== null && $filter !== '') {
            $known = array_column(self::definedChecks(), 'key');
            if (! in_array($filter, $known, true)) {
                $this->error("Check desconhecido: {$filter}");

                return self::FAILURE;
            }
        }

        $this->info('=== Auditoria de órfãos (integridade referencial) ===');
        $this->newLine();

        $audit = self::audit($filter ?: null);

        foreach ($audit['results'] as $row) {
            match ($row['status']) {
                'skipped' => $this->warn("  [skip] {$row['key']} — tabela/coluna ausente"),
                'orphans' => $this->error("  [órfãos] {$row['key']}: {$row['count']} — {$row['label']}"),
                default => $this->line("  [ok] {$row['key']}: 0 — {$row['label']}"),
            };
        }

        $this->newLine();
        $this->line("Total órfãos: {$audit['total_orphans']} | Checks com falha: {$audit['failed_checks']} | Ignorados: {$audit['skipped']}");

        if ($audit['failed_checks'] > 0 && $this->option('fail-on-orphans')) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param  array{child_table: string, child_column: string, parent_table: string, parent_column: string, nullable: bool}  $check
     */
    private static function countOrphansForCheck(array $check): int
    {
        $child = $check['child_table'];
        $childCol = $check['child_column'];
        $parent = $check['parent_table'];
        $parentCol = $check['parent_column'];

        $query = DB::table("{$child} as c")
            ->leftJoin("{$parent} as p", "c.{$childCol}", '=', "p.{$parentCol}")
            ->whereNull("p.{$parentCol}");

        if ($check['nullable']) {
            $query->whereNotNull("c.{$childCol}");
        }

        return (int) $query->count();
    }
}
