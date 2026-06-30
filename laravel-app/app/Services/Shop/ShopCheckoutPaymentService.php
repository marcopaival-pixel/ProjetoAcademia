<?php

namespace App\Services\Shop;

use App\Contracts\PaymentGatewayInterface;
use App\Models\ShopOrder;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Support\Facades\Log;

class ShopCheckoutPaymentService
{
    public function __construct(
        private PaymentGatewayManager $paymentManager,
        private ShopOrderService $orderService,
        private ShopPointsService $pointsService,
    ) {}

    /**
     * @return array{mode: string, redirect_url?: string|null}
     */
    public function initiate(ShopOrder $order, User $user, string $method): array
    {
        $order->loadMissing('items');

        $order->update(['payment_method' => $method]);

        if ($method === 'points') {
            $this->pointsService->payOrderWithPoints($user, $order, $this->orderService);

            return ['mode' => 'immediate'];
        }

        $gateway = $this->paymentManager->driver();

        $order->update(['payment_gateway' => $gateway->getIdentifier()]);

        $pagamentoAtivo = SystemSetting::where('key', 'pagamento_ativo')->first()?->value === 'true';

        if (! $pagamentoAtivo) {
            $this->orderService->markAsPaid($order, 'test-shop-'.$order->id, 'internal');

            Log::info('ShopCheckoutPaymentService: pedido marcado como pago (pagamento_ativo=false)', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            return ['mode' => 'immediate'];
        }

        return $this->initiateGatewayCheckout($order, $user, $gateway);
    }

    private function initiateGatewayCheckout(ShopOrder $order, User $user, PaymentGatewayInterface $gateway): array
    {
        $itemCount = $order->items->count();
        $response = $gateway->createCheckout($user, (float) $order->total, [
            'title' => 'Pedido '.$order->order_number,
            'description' => "Shopping Fitness — {$itemCount} item(ns)",
            'external_reference' => 'shop:'.$order->id,
            'success_url' => route('shopping.checkout.success', $order),
            'pending_url' => route('shopping.checkout.success', $order),
            'failure_url' => route('shopping.cart.index'),
        ]);

        if (empty($response['ok'])) {
            throw new \RuntimeException('Erro ao processar com o gateway: '.($response['error'] ?? 'resposta inválida'));
        }

        $preferenceId = $response['data']['id'] ?? $response['id'] ?? null;

        $order->update([
            'gateway_payment_id' => $preferenceId,
            'payment_gateway' => $gateway->getIdentifier(),
            'gateway_status' => 'pending',
        ]);

        return [
            'mode' => 'redirect',
            'redirect_url' => $response['init_point'] ?? $response['data']['init_point'] ?? null,
        ];
    }
}
