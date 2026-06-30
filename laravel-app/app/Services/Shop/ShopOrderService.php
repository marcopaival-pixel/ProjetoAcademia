<?php

namespace App\Services\Shop;

use App\Jobs\RefreshShopRecommendationsJob;
use App\Models\ShopCart;
use App\Models\ShopCoupon;
use App\Models\ShopCouponUsage;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopOrderService
{
    public function __construct(
        private ShopCartService $cartService,
        private ShopStockService $stockService,
        private ShopOrderRefundService $refundService,
        private ShopPointsService $pointsService,
        private ShopOrderNotificationService $notificationService,
    ) {}

    /**
     * Cria um pedido a partir do carrinho do usuário.
     * Toda a operação é envolvida em transação DB.
     *
     * @throws \RuntimeException
     */
    public function createFromCart(User $user, array $checkoutData): ShopOrder
    {
        return DB::transaction(function () use ($user, $checkoutData) {
            $summary = $this->cartService->summary($user);
            $cart    = $summary['cart'];

            if ($cart->isEmpty()) {
                throw new \RuntimeException('O carrinho está vazio.');
            }

            // Valida estoque para todos os itens físicos antes de criar o pedido
            foreach ($cart->items as $item) {
                if ($item->product->isPhysical() && $item->product->manage_stock) {
                    $this->stockService->assertAvailable($item->product, $item->quantity);
                }
            }

            // Cria o pedido
            $order = ShopOrder::create([
                'academy_company_id' => $user->academy_company_id,
                'user_id'            => $user->id,
                'coupon_id'          => $summary['coupon']?->id,
                'subtotal'           => $summary['subtotal'],
                'discount_amount'    => $summary['discount'],
                'shipping_amount'    => $checkoutData['shipping_amount'] ?? $summary['shipping'],
                'tax_amount'         => 0,
                'total'              => $summary['total'],
                'payment_method'     => $checkoutData['payment_method'] ?? null,
                'shipping_method'    => $checkoutData['shipping_method'] ?? null,
                'shipping_address'   => $checkoutData['shipping_address'] ?? null,
                'notes'              => $checkoutData['notes'] ?? null,
                'status'             => ShopOrder::STATUS_PENDING,
            ]);

            // Cria os itens do pedido (snapshot dos dados do produto)
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;

                $itemTotal = round($cartItem->unit_price * $cartItem->quantity, 2);
                $vendor    = $product->vendor;

                $commissionRate   = $vendor->commission_rate ?? 0;
                $commissionAmount = $commissionRate > 0
                    ? round($itemTotal * $commissionRate / 100, 2)
                    : null;

                $orderItem = ShopOrderItem::create([
                    'order_id'          => $order->id,
                    'product_id'        => $product->id,
                    'vendor_id'         => $product->vendor_id,
                    'product_name'      => $product->name,
                    'product_sku'       => $product->sku,
                    'product_type'      => $product->type,
                    'quantity'          => $cartItem->quantity,
                    'unit_price'        => $cartItem->unit_price,
                    'discount_amount'   => 0,
                    'total'             => $itemTotal,
                    'commission_rate'   => $commissionRate > 0 ? $commissionRate : null,
                    'commission_amount' => $commissionAmount,
                    'commission_status' => $commissionAmount ? 'pending' : null,
                ]);

                // Gera token de download para produtos digitais
                if ($product->isDigital()) {
                    $expiresAt = $product->download_expiry_days
                        ? now()->addDays($product->download_expiry_days)
                        : null;

                    $orderItem->update([
                        'download_token'      => Str::uuid()->toString(),
                        'download_expires_at' => $expiresAt,
                    ]);
                }

                // Baixa do estoque para produtos físicos
                if ($product->isPhysical() && $product->manage_stock) {
                    $this->stockService->decrement($product, $cartItem->quantity);
                }
            }

            // Registra uso do cupom
            if ($summary['coupon']) {
                $this->recordCouponUsage($summary['coupon'], $order, $user, $summary['discount']);
            }

            // Limpa o carrinho
            $this->cartService->clearCart($user);

            return $order->load('items');
        });
    }

    /**
     * Marca o pedido como pago após confirmação do gateway.
     */
    public function markAsPaid(ShopOrder $order, string $gatewayPaymentId, string $gateway): void
    {
        $order->update([
            'status'             => ShopOrder::STATUS_PAID,
            'gateway_payment_id' => $gatewayPaymentId,
            'payment_gateway'    => $gateway,
            'paid_at'            => now(),
        ]);

        $this->pointsService->awardCashbackForOrder($order->fresh(['user']));
        $this->notificationService->notifyOrderPaid($order->fresh(['user']));

        RefreshShopRecommendationsJob::dispatch($order->user_id);
    }

    /**
     * Cancela um pedido e devolve o estoque.
     *
     * @throws \RuntimeException
     */
    public function cancel(ShopOrder $order, string $reason = ''): void
    {
        if (! $order->isCancellable()) {
            throw new \RuntimeException('Este pedido não pode ser cancelado.');
        }

        $wasPaid = $order->status === ShopOrder::STATUS_PAID;

        DB::transaction(function () use ($order, $reason, $wasPaid) {
            $order->load('items.product');

            if ($wasPaid) {
                $this->refundService->refundPayments($order);
            }

            foreach ($order->items as $item) {
                if ($item->product_type === ShopProduct::TYPE_PHYSICAL) {
                    $product = $item->product;
                    if ($product && $product->manage_stock) {
                        $this->stockService->increment($product, $item->quantity);
                    }
                }
            }

            $order->update([
                'status' => $wasPaid ? ShopOrder::STATUS_REFUNDED : ShopOrder::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            $this->notificationService->notifyOrderCancelled($order->fresh(['user']), $reason);
        });
    }

    // ── Privados ───────────────────────────────────────────────────────────────

    private function recordCouponUsage(ShopCoupon $coupon, ShopOrder $order, User $user, float $discount): void
    {
        ShopCouponUsage::create([
            'coupon_id'        => $coupon->id,
            'order_id'         => $order->id,
            'user_id'          => $user->id,
            'discount_applied' => $discount,
        ]);

        $coupon->increment('used_count');
    }
}
