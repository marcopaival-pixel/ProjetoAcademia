<?php

namespace App\Services\Shop;

use App\Models\ShopProduct;
use Illuminate\Support\Facades\DB;

class ShopStockService
{
    /**
     * Verifica se há estoque suficiente.
     *
     * @throws \RuntimeException
     */
    public function assertAvailable(ShopProduct $product, int $quantity): void
    {
        if (! $product->manage_stock) {
            return;
        }

        if (($product->stock_quantity ?? 0) < $quantity) {
            throw new \RuntimeException(
                "Estoque insuficiente para \"{$product->name}\". Disponível: {$product->stock_quantity}."
            );
        }
    }

    /**
     * Decrementa o estoque em transação atômica para evitar race condition.
     *
     * @throws \RuntimeException
     */
    public function decrement(ShopProduct $product, int $quantity): void
    {
        DB::transaction(function () use ($product, $quantity) {
            // Bloqueia a linha para garantir atomicidade
            $fresh = ShopProduct::lockForUpdate()->find($product->id);

            if ($fresh === null) {
                throw new \RuntimeException("Produto não encontrado.");
            }

            if ($fresh->manage_stock && ($fresh->stock_quantity ?? 0) < $quantity) {
                throw new \RuntimeException(
                    "Estoque insuficiente para \"{$fresh->name}\"."
                );
            }

            if ($fresh->manage_stock) {
                $fresh->decrement('stock_quantity', $quantity);
            }
        });
    }

    /**
     * Incrementa o estoque (devolução / cancelamento).
     */
    public function increment(ShopProduct $product, int $quantity): void
    {
        if ($product->manage_stock) {
            $product->increment('stock_quantity', $quantity);
        }
    }

    /**
     * Ajuste manual de estoque (admin).
     */
    public function adjust(ShopProduct $product, int $newQuantity): void
    {
        if ($product->manage_stock) {
            $product->update(['stock_quantity' => max(0, $newQuantity)]);
        }
    }
}
