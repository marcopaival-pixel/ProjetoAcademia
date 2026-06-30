<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\AdminLog;
use App\Services\Shop\ShopOrderNotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ShopAdminOrderController extends Controller
{
    public function __construct(private ShopOrderNotificationService $notificationService) {}

    public function index(Request $request): View
    {
        $query = ShopOrder::query()->with(['user', 'items']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending_count' => ShopOrder::where('status', ShopOrder::STATUS_PENDING)->count(),
            'paid_today' => (float) ShopOrder::where('status', ShopOrder::STATUS_PAID)
                ->whereDate('paid_at', today())
                ->sum('total'),
            'month_revenue' => (float) ShopOrder::query()
                ->whereIn('status', [
                    ShopOrder::STATUS_PAID,
                    ShopOrder::STATUS_PROCESSING,
                    ShopOrder::STATUS_SHIPPED,
                    ShopOrder::STATUS_DELIVERED,
                    ShopOrder::STATUS_COMPLETED,
                ])
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total'),
        ];

        return view('admin.shop.orders.index', compact('orders', 'stats'));
    }

    public function show(ShopOrder $order): View
    {
        $order->load(['user', 'items.product', 'coupon']);
        return view('admin.shop.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, ShopOrder $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,completed,cancelled,refunded',
            'tracking_code' => 'nullable|string|max:100',
        ]);

        $status = $request->input('status');
        $trackingCode = $request->input('tracking_code');
        $previousStatus = $order->status;

        $updateData = ['status' => $status];

        if ($trackingCode !== null) {
            $updateData['tracking_code'] = $trackingCode;
        }

        // Registrar datas importantes no ciclo de vida
        if ($status === ShopOrder::STATUS_PAID && !$order->paid_at) {
            $updateData['paid_at'] = now();
        } elseif ($status === ShopOrder::STATUS_SHIPPED && !$order->shipped_at) {
            $updateData['shipped_at'] = now();
        } elseif ($status === ShopOrder::STATUS_DELIVERED && !$order->delivered_at) {
            $updateData['delivered_at'] = now();
        } elseif ($status === ShopOrder::STATUS_CANCELLED && !$order->cancelled_at) {
            $updateData['cancelled_at'] = now();
        }

        $order->update($updateData);

        if ($status === ShopOrder::STATUS_SHIPPED && $previousStatus !== ShopOrder::STATUS_SHIPPED) {
            $this->notificationService->notifyOrderShipped($order->fresh(['user']));
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Alterou status do pedido {$order->order_number} para {$status}",
            'ip_address' => $request->ip(),
            'payload' => $updateData
        ]);

        return redirect()->route('admin.shop.orders.show', $order)->with('success', 'Status do pedido atualizado com sucesso.');
    }

    public function refund(Request $request, ShopOrder $order): RedirectResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $order->isCancellable()) {
            return back()->with('error', 'Este pedido não pode ser reembolsado no estado atual.');
        }

        try {
            app(\App\Services\Shop\ShopOrderService::class)->cancel(
                $order,
                $request->input('reason', 'Reembolso administrativo')
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Reembolsou/cancelou pedido {$order->order_number}",
            'ip_address' => $request->ip(),
            'payload' => ['order_id' => $order->id, 'status' => $order->fresh()->status],
        ]);

        return redirect()->route('admin.shop.orders.show', $order)
            ->with('success', 'Pedido reembolsado/cancelado com sucesso.');
    }
}
