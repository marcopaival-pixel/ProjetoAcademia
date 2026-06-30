<?php

namespace App\Console\Commands;

use App\Models\AdminLog;
use App\Models\ApiAccessLog;
use App\Models\AuthAuditLog;
use App\Models\ClientErrorLog;
use App\Models\SystemError;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProduct;
use App\Services\Operations\SystemHealthService;
use App\Services\PaymentReconciliationService;
use App\Support\AppVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AuditReportCommand extends Command
{
    protected $signature = 'app:audit:report {--days=7 : Período em dias} {--output= : Caminho do ficheiro JSON (opcional)}';

    protected $description = 'Gera relatório consolidado de auditoria e observabilidade (JSON).';

    public function handle(SystemHealthService $healthService, PaymentReconciliationService $reconciliation): int
    {
        $days = max(1, (int) $this->option('days'));
        $since = now()->subDays($days);

        $report = [
            'generated_at' => now()->toIso8601String(),
            'app_version' => AppVersion::display(),
            'period_days' => $days,
            'since' => $since->toIso8601String(),
            'health' => $healthService->checkAll(),
            'counts' => $this->countLogs($since),
            'top_admin_actions' => $this->topAdminActions($since),
            'auth_summary' => $this->authSummary($since),
            'financial_reconciliation' => $reconciliation->analyze(7, $since),
            'database_orphans' => DatabaseOrphansCommand::audit(),
            'table_model_gap_shop' => DatabaseTableModelGapCommand::audit('shop_'),
            'shop' => $this->shopSnapshot($since),
        ];

        $json = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $output = $this->option('output');
        if ($output) {
            File::ensureDirectoryExists(dirname($output));
            File::put($output, $json);
            $this->info("Relatório gravado em: {$output}");
        } else {
            $defaultPath = storage_path('app/reports/audit-'.now()->format('Y-m-d_His').'.json');
            File::ensureDirectoryExists(dirname($defaultPath));
            File::put($defaultPath, $json);
            $this->info("Relatório gravado em: {$defaultPath}");
        }

        return self::SUCCESS;
    }

    private function countLogs(\Carbon\Carbon $since): array
    {
        $counts = [];

        foreach ([
            'admin_logs' => AdminLog::class,
            'auth_audit_logs' => AuthAuditLog::class,
            'api_access_logs' => ApiAccessLog::class,
            'client_error_logs' => ClientErrorLog::class,
            'system_errors' => SystemError::class,
        ] as $key => $model) {
            if (! Schema::hasTable((new $model)->getTable())) {
                $counts[$key] = null;

                continue;
            }

            $counts[$key] = $model::where('created_at', '>=', $since)->count();
        }

        return $counts;
    }

    private function topAdminActions(\Carbon\Carbon $since): array
    {
        if (! Schema::hasTable('admin_logs')) {
            return [];
        }

        return AdminLog::query()
            ->where('created_at', '>=', $since)
            ->selectRaw('action, COUNT(*) as total')
            ->groupBy('action')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => ['action' => $row->action, 'total' => (int) $row->total])
            ->all();
    }

    private function authSummary(\Carbon\Carbon $since): array
    {
        if (! Schema::hasTable('auth_audit_logs')) {
            return [];
        }

        return AuthAuditLog::query()
            ->where('created_at', '>=', $since)
            ->selectRaw('event, success, COUNT(*) as total')
            ->groupBy('event', 'success')
            ->get()
            ->map(fn ($row) => [
                'event' => $row->event,
                'success' => (bool) $row->success,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    private function shopSnapshot(\Carbon\Carbon $since): array
    {
        if (! Schema::hasTable('shop_orders')) {
            return ['available' => false];
        }

        $paidStatuses = [
            ShopOrder::STATUS_PAID,
            ShopOrder::STATUS_PROCESSING,
            ShopOrder::STATUS_SHIPPED,
            ShopOrder::STATUS_DELIVERED,
            ShopOrder::STATUS_COMPLETED,
        ];

        $lowStock = 0;
        if (Schema::hasTable('shop_products')) {
            $lowStock = ShopProduct::query()
                ->where('manage_stock', true)
                ->where('is_active', true)
                ->whereNotNull('stock_alert_threshold')
                ->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                ->count();
        }

        return [
            'available' => true,
            'orders_period' => ShopOrder::where('created_at', '>=', $since)->count(),
            'orders_paid_period' => ShopOrder::query()
                ->whereIn('status', $paidStatuses)
                ->where('paid_at', '>=', $since)
                ->count(),
            'revenue_paid_period' => (float) ShopOrder::query()
                ->whereIn('status', $paidStatuses)
                ->where('paid_at', '>=', $since)
                ->sum('total'),
            'pending_orders' => ShopOrder::where('status', ShopOrder::STATUS_PENDING)->count(),
            'low_stock_products' => $lowStock,
            'pending_commissions' => Schema::hasTable('shop_order_items')
                ? (float) ShopOrderItem::query()
                    ->whereIn('commission_status', ['pending', 'released'])
                    ->whereNotNull('commission_amount')
                    ->sum('commission_amount')
                : 0.0,
        ];
    }
}
