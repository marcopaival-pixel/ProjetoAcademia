<?php

namespace App\Services\Shop;

use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopVendor;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShopSalesReportService
{
    /** @var list<string> */
    private const REVENUE_STATUSES = [
        ShopOrder::STATUS_PAID,
        ShopOrder::STATUS_PROCESSING,
        ShopOrder::STATUS_SHIPPED,
        ShopOrder::STATUS_DELIVERED,
        ShopOrder::STATUS_COMPLETED,
    ];

    /**
     * @return array{
     *     from: string,
     *     to: string,
     *     order_count: int,
     *     gross_revenue: float,
     *     discount_total: float,
     *     shipping_total: float,
     *     net_revenue: float,
     *     pending_commissions: float,
     *     paid_commissions: float,
     *     by_vendor: Collection,
     *     top_products: Collection,
     *     by_status: Collection
     * }
     */
    public function summary(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = ($from ?? now()->startOfMonth())->copy()->startOfDay();
        $to = ($to ?? now())->copy()->endOfDay();

        $ordersQuery = ShopOrder::query()
            ->whereIn('status', self::REVENUE_STATUSES)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to]);

        $orderCount = (clone $ordersQuery)->count();
        $grossRevenue = (float) (clone $ordersQuery)->sum('total');
        $discountTotal = (float) (clone $ordersQuery)->sum('discount_amount');
        $shippingTotal = (float) (clone $ordersQuery)->sum('shipping_amount');

        $itemsQuery = ShopOrderItem::query()
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->whereIn('status', self::REVENUE_STATUSES)
                    ->whereNotNull('paid_at')
                    ->whereBetween('paid_at', [$from, $to]);
            })
            ->whereNotNull('commission_amount')
            ->where('commission_amount', '>', 0);

        $pendingCommissions = (float) (clone $itemsQuery)
            ->whereIn('commission_status', ['pending', 'released'])
            ->sum('commission_amount');

        $paidCommissions = (float) (clone $itemsQuery)
            ->where('commission_status', 'paid')
            ->sum('commission_amount');

        $byVendor = ShopOrderItem::query()
            ->select([
                'vendor_id',
                DB::raw('SUM(commission_amount) as commission_total'),
                DB::raw("SUM(CASE WHEN commission_status = 'paid' THEN commission_amount ELSE 0 END) as commission_paid"),
                DB::raw("SUM(CASE WHEN commission_status IN ('pending', 'released') THEN commission_amount ELSE 0 END) as commission_pending"),
                DB::raw('COUNT(*) as item_count'),
            ])
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->whereIn('status', self::REVENUE_STATUSES)
                    ->whereNotNull('paid_at')
                    ->whereBetween('paid_at', [$from, $to]);
            })
            ->whereNotNull('vendor_id')
            ->whereNotNull('commission_amount')
            ->where('commission_amount', '>', 0)
            ->groupBy('vendor_id')
            ->get()
            ->map(function ($row) {
                $vendor = ShopVendor::find($row->vendor_id);

                return [
                    'vendor_id' => (int) $row->vendor_id,
                    'vendor_name' => $vendor?->name ?? '—',
                    'commission_total' => (float) $row->commission_total,
                    'commission_paid' => (float) $row->commission_paid,
                    'commission_pending' => (float) $row->commission_pending,
                    'item_count' => (int) $row->item_count,
                ];
            })
            ->sortByDesc('commission_total')
            ->values();

        $topProducts = ShopOrderItem::query()
            ->select([
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as units_sold'),
                DB::raw('SUM(total) as revenue'),
            ])
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->whereIn('status', self::REVENUE_STATUSES)
                    ->whereNotNull('paid_at')
                    ->whereBetween('paid_at', [$from, $to]);
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $byStatus = ShopOrder::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'order_count' => $orderCount,
            'gross_revenue' => $grossRevenue,
            'discount_total' => $discountTotal,
            'shipping_total' => $shippingTotal,
            'net_revenue' => $grossRevenue,
            'pending_commissions' => $pendingCommissions,
            'paid_commissions' => $paidCommissions,
            'by_vendor' => $byVendor,
            'top_products' => $topProducts,
            'by_status' => $byStatus,
        ];
    }

    public function markVendorCommissionsPaid(int $vendorId, ?Carbon $until = null): int
    {
        $until = ($until ?? now())->copy()->endOfDay();

        return ShopOrderItem::query()
            ->where('vendor_id', $vendorId)
            ->whereIn('commission_status', ['pending', 'released'])
            ->whereNotNull('commission_amount')
            ->where('commission_amount', '>', 0)
            ->whereHas('order', function ($q) use ($until) {
                $q->whereIn('status', self::REVENUE_STATUSES)
                    ->whereNotNull('paid_at')
                    ->where('paid_at', '<=', $until);
            })
            ->update(['commission_status' => 'paid']);
    }

    /**
     * @return \Illuminate\Support\Collection<int, ShopOrder>
     */
    public function ordersForExport(Carbon $from, Carbon $to)
    {
        return ShopOrder::query()
            ->with('user:id,name,email')
            ->whereIn('status', self::REVENUE_STATUSES)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->orderByDesc('paid_at')
            ->get();
    }
}
