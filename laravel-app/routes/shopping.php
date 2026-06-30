<?php

use App\Http\Controllers\Shopping\ShopCartController;
use App\Http\Controllers\Shopping\ShopCheckoutController;
use App\Http\Controllers\Shopping\ShopController;
use App\Http\Controllers\Shopping\ShopDigitalDownloadController;
use App\Http\Controllers\Shopping\ShopOrderController;
use App\Http\Controllers\Shopping\ShopPointsController;
use App\Http\Controllers\Shopping\ShopProductController;
use App\Http\Controllers\Shopping\ShopWishlistController;
use Illuminate\Support\Facades\Route;

/**
 * Módulo Shopping Fitness
 * -----------------------------------------------------------------
 * Carregado dentro do grupo middleware auth em web.php.
 * Prefixo: /shopping  |  Name prefix: shopping.
 */

Route::prefix('shopping')->name('shopping.')->group(function () {

    // ── Vitrine ───────────────────────────────────────────────────
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/busca', [ShopController::class, 'search'])->name('search');
    Route::get('/categoria/{slug}', [ShopController::class, 'category'])->name('category');

    // ── Produto ───────────────────────────────────────────────────
    Route::get('/produto/{slug}', [ShopProductController::class, 'show'])->name('product.show');

    // ── Carrinho ──────────────────────────────────────────────────
    Route::prefix('carrinho')->name('cart.')->group(function () {
        Route::get('/', [ShopCartController::class, 'index'])->name('index');
        Route::post('/adicionar', [ShopCartController::class, 'add'])->name('add');
        Route::patch('/item/{cartItemId}', [ShopCartController::class, 'update'])->name('update');
        Route::delete('/item/{cartItemId}', [ShopCartController::class, 'remove'])->name('remove');
        Route::post('/cupom', [ShopCartController::class, 'applyCoupon'])->name('coupon.apply');
        Route::delete('/cupom', [ShopCartController::class, 'removeCoupon'])->name('coupon.remove');
    });

    // ── Checkout ──────────────────────────────────────────────────
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [ShopCheckoutController::class, 'index'])->name('index');
        Route::post('/', [ShopCheckoutController::class, 'process'])->name('process');
        Route::get('/obrigado/{order}', [ShopCheckoutController::class, 'success'])->name('success');
    });

    // ── Pedidos ───────────────────────────────────────────────────
    Route::prefix('pedidos')->name('orders.')->group(function () {
        Route::get('/', [ShopOrderController::class, 'index'])->name('index');
        Route::get('/download/{token}', ShopDigitalDownloadController::class)->name('download');
        Route::get('/{order}', [ShopOrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancelar', [ShopOrderController::class, 'cancel'])->name('cancel');
    });

    // ── Wishlist ──────────────────────────────────────────────────
    Route::post('/desejos/{productId}', [ShopWishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/desejos', [ShopWishlistController::class, 'index'])->name('wishlist.index');

    // ── Pontos de fidelidade ───────────────────────────────────────
    Route::get('/pontos', [ShopPointsController::class, 'index'])->name('points.index');
});
