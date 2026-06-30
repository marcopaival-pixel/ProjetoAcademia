<?php

namespace App\Services\Shop;

use App\Models\Payment;
use App\Models\ShopOrder;
use App\Services\FinancialLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopPaymentFulfillmentService
{
    public function __construct(private ShopOrderService $orderService) {}

    public function markOrderPaidFromGateway(
        int $orderId,
        string $gatewayPaymentId,
        string $gateway,
        ?float $amount = null
    ): bool {
        $order = ShopOrder::find($orderId);

        if ($order === null) {
            Log::warning('ShopPaymentFulfillment: pedido não encontrado.', ['order_id' => $orderId]);

            return false;
        }

        if ($order->isPaid()) {
            return false;
        }

        return DB::transaction(function () use ($order, $gatewayPaymentId, $gateway, $amount) {
            Payment::updateOrCreate(
                ['gateway' => $gateway, 'gateway_id' => $gatewayPaymentId],
                [
                    'user_id' => $order->user_id,
                    'amount' => $amount ?? (float) $order->total,
                    'currency' => 'BRL',
                    'status' => 'paid',
                    'payload' => ['shop_order_id' => $order->id, 'order_number' => $order->order_number],
                ]
            );

            $this->orderService->markAsPaid($order, $gatewayPaymentId, $gateway);

            FinancialLogService::log([
                'user_id' => $order->user_id,
                'action' => 'SHOP_ORDER_PAID',
                'amount' => $amount ?? (float) $order->total,
                'transaction_id' => $gatewayPaymentId,
                'origin' => $gateway,
                'payload' => [
                    'shop_order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            return true;
        });
    }
}
