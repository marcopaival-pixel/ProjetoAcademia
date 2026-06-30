<?php

namespace App\Http\Controllers\Shopping;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Services\Shop\ShopCartService;
use App\Services\Shop\ShopOrderAccess;
use App\Services\Shop\ShopOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopOrderController extends Controller
{
    public function __construct(
        private ShopOrderService $orderService,
        private ShopCartService $cartService,
        private ShopOrderAccess $orderAccess,
    ) {}

    public function index()
    {
        $orders = ShopOrder::where('user_id', Auth::id())
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('shopping.orders.index', compact('orders'));
    }

    public function show(ShopOrder $order)
    {
        $this->authorizeOrder($order);

        $order->load('items.product.images', 'coupon');

        return view('shopping.orders.show', compact('order'));
    }

    public function cancel(Request $request, ShopOrder $order)
    {
        $this->authorizeOrder($order);

        try {
            $this->orderService->cancel($order, $request->input('reason', ''));
            return back()->with('success', 'Pedido cancelado com sucesso.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizeOrder(ShopOrder $order): void
    {
        $this->orderAccess->assertOwnedBy($order);
    }
}
