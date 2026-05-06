<?php

use App\Http\Controllers\Professional\AIPrescriptionController;
use App\Http\Controllers\Professional\BrandingController;
use App\Http\Controllers\Professional\CouponController;
use App\Http\Controllers\Professional\DashboardController as ProfessionalDashboardController;
use App\Http\Controllers\Professional\PatientController;
use App\Http\Controllers\Professional\PatientRequestController;
use App\Http\Controllers\Professional\SubscriptionController;
use App\Http\Controllers\Patient\ProfessionalSearchController;
use App\Http\Controllers\Professional\ProfessionalProfileController;
use App\Http\Controllers\Professional\MedicalRecordController;
use Illuminate\Support\Facades\Route;

// Painel do Profissional (Portal Pro)
Route::middleware(['auth'])->prefix('professional')->name('professional.')->group(function () {
    
    Route::get('/profile', [ProfessionalProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfessionalProfileController::class, 'update'])->name('profile.update');

    // Rota de visualização/vínculo (usada no QR Code e busca)
    Route::get('/link/{code}', [ProfessionalSearchController::class, 'show'])->name('link');

    Route::get('/dashboard', [ProfessionalDashboardController::class, 'index'])->name('dashboard');

    // Gestão de Pacientes
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('index');
        Route::get('/create', [PatientController::class, 'create'])->name('create');
        Route::post('/', [PatientController::class, 'store'])->name('store');
        Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
        Route::put('/{patient}', [PatientController::class, 'update'])->name('update');
        Route::get('/{patient}', [PatientController::class, 'show'])->name('show');
        Route::post('/{patient}/transfer', [PatientController::class, 'transfer'])->name('transfer');
        Route::post('/{patient}/deactivate', [PatientController::class, 'deactivate'])->name('deactivate');
        Route::get('/{patient}/generate-link', [PatientController::class, 'generateAccessLink'])->name('generate-link');
        Route::get('/{patient}/activation-link', [PatientController::class, 'resendActivationLink'])->name('resend-activation');
        Route::get('/{patient}/export-report', [\App\Http\Controllers\Professional\PatientReportController::class, 'export'])->name('export-report');

        // Prontuário / Laudos
        Route::prefix('{patient}/medical-records')->name('medical-records.')->group(function () {
            Route::get('/', [MedicalRecordController::class, 'index'])->name('index');
            Route::get('/summary', [MedicalRecordController::class, 'summary'])->name('summary');
            Route::post('/summary', [MedicalRecordController::class, 'updateSummary'])->name('summary.update');
            
            Route::prefix('evolutions')->name('evolutions.')->group(function () {
                Route::get('/', [MedicalRecordController::class, 'evolutions'])->name('index');
                Route::post('/', [MedicalRecordController::class, 'storeEvolution'])->name('store');
            });

            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [MedicalRecordController::class, 'reports'])->name('index');
                Route::post('/', [MedicalRecordController::class, 'storeReport'])->name('store');
                Route::get('/{report}/download', [MedicalRecordController::class, 'downloadReport'])->name('download');
            });

            Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
                Route::get('/', [MedicalRecordController::class, 'prescriptions'])->name('index');
                Route::post('/', [MedicalRecordController::class, 'storePrescription'])->name('store');
            });

            Route::prefix('certificates')->name('certificates.')->group(function () {
                Route::get('/', [MedicalRecordController::class, 'certificates'])->name('index');
                Route::post('/', [MedicalRecordController::class, 'storeCertificate'])->name('store');
                Route::get('/{certificate}/download', [MedicalRecordController::class, 'downloadCertificate'])->name('download');
            });

            Route::get('/documents', [MedicalRecordController::class, 'documents'])->name('documents');
            Route::get('/history', [MedicalRecordController::class, 'history'])->name('history');
        });
    });

    // Solicitações de Vínculo
    Route::prefix('requests')->name('requests.')->group(function () {
        Route::get('/', [PatientRequestController::class, 'index'])->name('index');
        Route::post('/{request}/approve', [PatientRequestController::class, 'approve'])->name('approve');
        Route::post('/{request}/reject', [PatientRequestController::class, 'reject'])->name('reject');
    });

    // Configurações e Assinatura
    Route::get('/branding', [BrandingController::class, 'index'])->name('branding');
    Route::post('/branding', [BrandingController::class, 'update'])->name('branding.update');
    
    Route::get('/billing', [SubscriptionController::class, 'index'])->name('billing.index');
    Route::get('/billing/upgrade', [SubscriptionController::class, 'upgrade'])->name('billing.upgrade');

    // Assistente IA (wizard) + prescrição
    Route::get('/ai-wizard', [AIPrescriptionController::class, 'index'])->name('ai-wizard.index');
    Route::post('/ai-wizard/generate', [AIPrescriptionController::class, 'generate'])->name('ai-wizard.generate');
    Route::post('/ai-wizard/store', [AIPrescriptionController::class, 'store'])->name('ai-wizard.store');
    
    // Templates de Prescrição
    Route::resource('templates', \App\Http\Controllers\Professional\PrescriptionTemplateController::class);

    // JSON history para o Wizard
    Route::get('/medical-records/{patient}/prescriptions-json', [MedicalRecordController::class, 'prescriptionsJson'])->name('medical-records.prescriptions-json');

    // Módulo de Relatórios (Monetizado)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Professional\ReportController::class, 'index'])->name('index');
        Route::get('/view/{type}', [App\Http\Controllers\Professional\ReportController::class, 'show'])->name('show');
        Route::get('/export/{type}', [App\Http\Controllers\Professional\ReportController::class, 'export'])->name('export');
    });

    // Cupons solicitados pelo profissional
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::get('/create', [CouponController::class, 'create'])->name('create');
        Route::post('/', [CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}', [CouponController::class, 'show'])->name('show');
    });

    // Agenda
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AgendaController::class, 'index'])->name('index');
        Route::post('/settings', [\App\Http\Controllers\AgendaController::class, 'updateSettings'])->name('settings.update');
        Route::post('/appointment/{appointment}/status', [\App\Http\Controllers\AgendaController::class, 'updateStatus'])->name('appointment.status');
    });
});
