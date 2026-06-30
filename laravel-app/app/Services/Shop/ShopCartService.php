<?php

namespace App\Services\Shop;

use App\Models\ShopCart;
use App\Models\ShopCartItem;
use App\Models\ShopCoupon;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShopCartService
{
    /**
     * Retorna ou cria o carrinho ativo do usuário.
     */
    public function getOrCreateCart(User $user): ShopCart
    {
        return ShopCart::firstOrCreate(
            [
                'user_id'            => $user->id,
                'academy_company_id' => $user->academy_company_id,
            ],
            ['expires_at' => now()->addDays(7)]
        );
    }

    /**
     * Adiciona ou incrementa um produto no carrinho.
     */
    public function addItem(User $user, int $productId, int $quantity = 1): ShopCartItem
    {
        $product = ShopProduct::published()->findOrFail($productId);

        if (! $product->isInStock()) {
            throw new \RuntimeException('Produto fora de estoque.');
        }

        $cart = $this->getOrCreateCart($user);

        $existing = $cart->items()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
            return $existing->fresh();
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'quantity'   => $quantity,
            'unit_price' => $product->currentPrice(),
        ]);
    }

    /**
     * Atualiza a quantidade de um item. Remove se quantity <= 0.
     */
    public function updateItem(User $user, int $cartItemId, int $quantity): void
    {
        $cart = $this->getOrCreateCart($user);
        $item = $cart->items()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    /**
     * Remove um item do carrinho.
     */
    public function removeItem(User $user, int $cartItemId): void
    {
        $cart = $this->getOrCreateCart($user);
        $cart->items()->where('id', $cartItemId)->delete();
    }

    /**
     * Limpa todos os itens do carrinho.
     */
    public function clearCart(User $user): void
    {
        $cart = $this->getOrCreateCart($user);
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);
    }

    /**
     * Aplica um cupom ao carrinho.
     *
     * @throws \RuntimeException
     */
    public function applyCoupon(User $user, string $code): ShopCoupon
    {
        $cart   = $this->getOrCreateCart($user);
        $coupon = ShopCoupon::where('code', strtoupper($code))
            ->where('academy_company_id', $user->academy_company_id)
            ->first();

        if (! $coupon) {
            throw new \RuntimeException('Cupom não encontrado.');
        }

        if (! $coupon->isValidForUser($user->id)) {
            throw new \RuntimeException('Cupom inválido ou já utilizado.');
        }

        $subtotal = $cart->subtotal();
        if (! $coupon->isValidForOrder($subtotal)) {
            $min = number_format((float) $coupon->minimum_order_value, 2, ',', '.');
            throw new \RuntimeException("Valor mínimo para este cupom é R$ {$min}.");
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return $coupon;
    }

    /**
     * Remove o cupom do carrinho.
     */
    public function removeCoupon(User $user): void
    {
        $cart = $this->getOrCreateCart($user);
        $cart->update(['coupon_id' => null]);
    }

    /**
     * Calcula o resumo do carrinho com desconto e frete.
     */
    public function summary(User $user): array
    {
        $cart     = $this->getOrCreateCart($user);
        $cart->load('items.product', 'coupon');

        $subtotal = $cart->subtotal();
        $discount = 0.0;
        $shipping = 0.0; // calculado na hora do checkout com endereço real

        $coupon = $cart->coupon;
        if ($coupon && $coupon->isValidForUser($user->id)) {
            $discount = $coupon->calculateDiscount($subtotal);
            if ($coupon->free_shipping) {
                $shipping = 0.0;
            }
        }

        return [
            'cart'     => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'total'    => max(0, $subtotal - $discount + $shipping),
            'coupon'   => $coupon,
        ];
    }
}
