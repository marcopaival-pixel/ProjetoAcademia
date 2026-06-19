<?php

use App\Http\Controllers\Representative\RepresentativeDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:representative', 'panel.isolation'])
    ->prefix('representative')
    ->name('representative.')
    ->group(function () {
    // Rotas do Portal do Representante
    Route::get('/dashboard', [RepresentativeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/commissions', [RepresentativeDashboardController::class, 'commissions'])->name('commissions');
    Route::get('/referrals', [RepresentativeDashboardController::class, 'referrals'])->name('referrals');
    Route::get('/my-codes', [RepresentativeDashboardController::class, 'myCodes'])->name('my-codes');
    
    // Novas rotas de saque
    Route::get('/withdraw', [RepresentativeDashboardController::class, 'withdrawForm'])->name('withdraw.form');
    Route::post('/withdraw', [RepresentativeDashboardController::class, 'withdrawStore'])->name('withdraw.store');
    Route::get('/withdraw/history', [RepresentativeDashboardController::class, 'withdrawHistory'])->name('withdraw.history');

    // Módulos Comerciais
    Route::resource('leads', \App\Http\Controllers\Representative\LeadController::class);
    Route::get('proposals/{proposal}/pdf', [\App\Http\Controllers\Representative\ProposalController::class, 'generatePdf'])->name('proposals.pdf');
    Route::resource('proposals', \App\Http\Controllers\Representative\ProposalController::class);
    Route::resource('contracts', \App\Http\Controllers\Representative\ContractController::class);
    Route::resource('clinics', \App\Http\Controllers\Representative\ClinicController::class)->only(['index', 'show']);
    
    // Simulador de Negociação
    Route::get('/simulator', [\App\Http\Controllers\Representative\SimulatorController::class, 'index'])->name('simulator.index');
    Route::post('/simulator/pdf', [\App\Http\Controllers\Representative\SimulatorController::class, 'generatePdf'])->name('simulator.pdf');
    
    // Agenda e Relatórios
    Route::get('agenda', [\App\Http\Controllers\Representative\AgendaController::class, 'index'])->name('agenda.index');
    Route::get('reports', [\App\Http\Controllers\Representative\ReportController::class, 'index'])->name('reports.index');
    
    // Perfil
    Route::get('profile', [\App\Http\Controllers\Representative\ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [\App\Http\Controllers\Representative\ProfileController::class, 'update'])->name('profile.update');
});
