<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegistrationStatusController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;

// Rotas de Verificação de E-mail (Públicas com Token)
Route::withoutMiddleware([EnsureEmailIsVerified::class])->group(function () {
    Route::get('/confirmar-email/{token}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::get('/verify-email/{token}', [VerificationController::class, 'verify']);
});

Route::get('/confirmar-email/sucesso', [VerificationController::class, 'success'])->middleware('auth')->name('email-verification.success');
Route::get('/confirmar-email/erro', [VerificationController::class, 'failed'])->name('email-verification.failed');

// Rotas para Visitantes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->middleware('throttle:5,1');
    
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    
    Route::post('/confirmar-email/reenviar', [VerificationController::class, 'resendGuest'])
        ->middleware('throttle:10,60')
        ->name('verification.resend.guest');

    // Recuperação de Senha
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Google Authentication
    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');

    // Acompanhar Cadastro (Público)
    Route::get('/cadastro/acompanhar', [RegistrationStatusController::class, 'track'])->name('registration.track');
    Route::post('/cadastro/acompanhar', [RegistrationStatusController::class, 'search'])->name('registration.search');
});

// Google Callback (External)
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Rotas Autenticadas (Auth)
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', LogoutController::class)->name('logout');
    
    Route::get('/cadastro/pendente', [RegistrationStatusController::class, 'pending'])->name('registration.pending');
    Route::get('/representante/pendente', [RegistrationStatusController::class, 'representativePending'])->name('representative.pending');
    Route::get('/cadastro/recusado', [RegistrationStatusController::class, 'rejected'])->name('registration.rejected');

    Route::get('/verify-email', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/verify-email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    // Troca de Senha Obrigatória (Reset Forçado)
    Route::get('/senha/obrigatoria', [\App\Http\Controllers\Auth\ForcedPasswordChangeController::class, 'show'])->name('password.change.force');
    Route::post('/senha/obrigatoria', [\App\Http\Controllers\Auth\ForcedPasswordChangeController::class, 'store'])->name('password.change.force.store');
});
