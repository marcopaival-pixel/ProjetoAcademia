<?php

use App\Http\Controllers\Representative\RepresentativeDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('representative')->name('representative.')->group(function () {
    // Rotas do Portal do Representante
    Route::get('/dashboard', [RepresentativeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/commissions', [RepresentativeDashboardController::class, 'commissions'])->name('commissions');
    Route::get('/referrals', [RepresentativeDashboardController::class, 'referrals'])->name('referrals');
    
    // Novas rotas de saque
    Route::get('/withdraw', [RepresentativeDashboardController::class, 'withdrawForm'])->name('withdraw.form');
    Route::post('/withdraw', [RepresentativeDashboardController::class, 'withdrawStore'])->name('withdraw.store');
    Route::get('/withdraw/history', [RepresentativeDashboardController::class, 'withdrawHistory'])->name('withdraw.history');
});
