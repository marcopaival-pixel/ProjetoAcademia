<?php

namespace App\Services\Shop;

use App\Models\Payment;
use App\Models\ShopOrder;
use App\Services\FinancialLogService;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Support\Facades\Log;

class ShopOrderRefundService
{
    public function __construct(
        private ShopPointsService $pointsService,
        private PaymentGatewayManager $paymentManager,
    ) {}

    public function refundPayments(ShopOrder $order): void
    {
        if ($order->payment_method === 'points') {
            $this->pointsService->refundOrderRedemption($order);

            return;
        }

        $this->pointsService->clawbackOrderCashback($order);

        if (in_array($order->payment_gateway, ['internal', 'points', null], true)) {
            return;
        }

        $payment = Payment::query()
            ->where('user_id', $order->user_id)
            ->where('gateway', $order->payment_gateway)
            ->where('gateway_id', $order->gateway_payment_id)
            ->where('status', 'paid')
            ->first();

        if ($payment === null) {
            return;
        }

        try {
            $this->paymentManager->driver($payment->gateway)->refund($payment->gateway_id, (float) $order->total);
        } catch (\Throwable $e) {
            Log::warning('ShopOrderRefund: falha no estorno do gateway — prosseguindo com estorno local.', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        $payment->update([
            'status' => 'refunded',
            'payload' => array_merge($payment->payload ?? [], [
                'refunded_at' => now()->toIso8601String(),
                'refund_origin' => 'shop_order_cancel',
                'shop_order_id' => $order->id,
            ]),
        ]);

        FinancialLogService::log([
            'user_id' => $order->user_id,
            'action' => 'SHOP_ORDER_REFUND',
            'amount' => (float) $order->total,
            'transaction_id' => $payment->gateway_id,
            'origin' => $payment->gateway,
            'payload' => ['shop_order_id' => $order->id],
        ]);
    }
}
