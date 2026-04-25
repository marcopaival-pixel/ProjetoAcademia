<?php

use App\Http\Controllers\MercadoPago\CheckoutStartController;
use App\Http\Controllers\MercadoPago\ReturnController as MpReturnController;
use App\Http\Controllers\MercadoPago\SubReturnController;
use App\Http\Controllers\MercadoPago\WebhookController;
use Illuminate\Support\Facades\Route;

// Webhooks (Públicos)
Route::post('/mp/webhook', WebhookController::class);
Route::post('/mp_webhook.php', WebhookController::class);

// Operações Financeiras (Autenticadas)
Route::middleware('auth')->group(function () {
    Route::post('/mp_start.php', CheckoutStartController::class)->name('mp.start');
    
    // Retornos do Checkout
    Route::prefix('mp')->name('mp.')->group(function () {
        Route::get('/return', MpReturnController::class)->name('return');
        Route::get('/sub-return', SubReturnController::class)->name('sub-return');
    });
});
