<?php

namespace App\Services\Shop;

use App\Models\ShopProduct;
use App\Models\User;
use App\Services\MessagingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ShopStockAlertService
{
    /**
     * @return Collection<int, ShopProduct>
     */
    public function lowStockProducts(): Collection
    {
        return ShopProduct::query()
            ->with(['category', 'vendor'])
            ->where('manage_stock', true)
            ->where('is_active', true)
            ->where('status', ShopProduct::STATUS_PUBLISHED)
            ->whereNotNull('stock_alert_threshold')
            ->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
            ->orderBy('stock_quantity')
            ->get();
    }

    public function outOfStockProducts(): Collection
    {
        return ShopProduct::query()
            ->with(['category', 'vendor'])
            ->where('manage_stock', true)
            ->where('is_active', true)
            ->where('status', ShopProduct::STATUS_PUBLISHED)
            ->where('stock_quantity', '<=', 0)
            ->orderBy('name')
            ->get();
    }

    public function notifyAdminsLowStock(): int
    {
        $products = $this->lowStockProducts();
        if ($products->isEmpty()) {
            return 0;
        }

        $lines = $products->take(25)->map(function (ShopProduct $product) {
            return sprintf(
                '- %s: %d un. (alerta ≤ %d)',
                $product->name,
                (int) $product->stock_quantity,
                (int) $product->stock_alert_threshold
            );
        })->implode("\n");

        $suffix = $products->count() > 25 ? "\n... e mais ".($products->count() - 25).' produto(s).' : '';

        $notified = 0;

        User::query()
            ->where('is_admin', true)
            ->where('status', 'active')
            ->orderBy('id')
            ->each(function (User $admin) use ($lines, $suffix, &$notified) {
                try {
                    MessagingService::sendSystemMessage(
                        $admin->id,
                        'Alerta de estoque — Shopping',
                        "Os seguintes produtos estão com estoque baixo ou zerado:\n\n{$lines}{$suffix}"
                    );
                    $notified++;
                } catch (\Throwable $e) {
                    Log::warning('ShopStockAlert: falha ao notificar admin.', [
                        'admin_id' => $admin->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            });

        return $notified;
    }
}
