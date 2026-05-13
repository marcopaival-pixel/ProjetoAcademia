<?php

use App\Http\Controllers\Patient\MeasurementController;
use App\Http\Controllers\Patient\PortalController;
use App\Http\Controllers\Patient\ProfessionalSearchController;
use App\Http\Controllers\Patient\ReportController;
use Illuminate\Support\Facades\Route;

// Acesso via Token (Sem middleware auth)
Route::get('/access', [PortalController::class, 'access'])->name('access')->middleware('throttle:5,1');

// Seleção de Profissional (Multi-vínculo)
Route::middleware(['auth'])->group(function() {
    Route::get('/patient/select-professional', [\App\Http\Controllers\Patient\ProfessionalSelectionController::class, 'index'])->name('patient.professional.selection');
    Route::post('/patient/select-professional', [\App\Http\Controllers\Patient\ProfessionalSelectionController::class, 'select'])->name('patient.professional.select');
    
    // Escolha entre Visão Geral ou Profissional Específico
    Route::get('/patient/dashboard-choice', [\App\Http\Controllers\Patient\UnifiedDashboardController::class, 'choice'])->name('patient.dashboard.choice');
    Route::get('/patient/unified-dashboard', [\App\Http\Controllers\Patient\UnifiedDashboardController::class, 'index'])->name('patient.unified.dashboard');
});

// Ativação de Conta (Primeiro Acesso - Sem login)
Route::get('/ativar-conta/{token}', [\App\Http\Controllers\Patient\PatientActivationController::class, 'show'])->name('patient.activate.show');
Route::post('/ativar-conta/{token}', [\App\Http\Controllers\Patient\PatientActivationController::class, 'activate'])->name('patient.activate.process');

// Complementação de Dados (Logado mas incompleto)
Route::middleware(['auth'])->group(function() {
    Route::get('/patient/complete-profile', [\App\Http\Controllers\Patient\PatientActivationController::class, 'completeProfileShow'])->name('patient.profile.complete');
    Route::post('/patient/complete-profile', [\App\Http\Controllers\Patient\PatientActivationController::class, 'completeProfileStore'])->name('patient.profile.store');
});

// Painel do Aluno / Paciente restricted to auth
Route::middleware([
    'auth', 
    \App\Http\Middleware\EnsurePatientProfileIsComplete::class,
    \App\Http\Middleware\EnsureProfessionalIsSelected::class
])->prefix('patient')->name('patient.')->group(function () {
    
    // Portal do Paciente (Somente Leitura)
    Route::get('/portal', [PortalController::class, 'index'])->name('portal');
    Route::get('/plans', [PortalController::class, 'plans'])->name('plans.index');
    Route::get('/treatment-plan', [PortalController::class, 'treatmentPlan'])->name('treatment-plan')->middleware('premium');
    Route::get('/evolution', [PortalController::class, 'evolution'])->name('evolution')->middleware('premium');
    Route::get('/prescriptions', [PortalController::class, 'prescriptions'])->name('prescriptions')->middleware('premium');
    Route::get('/documents', [PortalController::class, 'documents'])->name('documents')->middleware('premium');
    Route::get('/agenda', [PortalController::class, 'agenda'])->name('agenda');
    Route::get('/messages', [PortalController::class, 'messages'])->name('messages')->middleware('premium');
    
    Route::prefix('medical-records')->name('medical-records.')->middleware('premium')->group(function() {
        Route::get('/', [PortalController::class, 'medicalRecords'])->name('index');
        Route::get('/evolutions', [PortalController::class, 'medicalEvolutions'])->name('evolutions');
        Route::get('/reports', [PortalController::class, 'medicalReports'])->name('reports');
        Route::get('/reports/{report}/download', [PortalController::class, 'downloadReport'])->name('reports.download');
        Route::get('/prescriptions', [PortalController::class, 'medicalPrescriptions'])->name('prescriptions');
        Route::get('/prescriptions/{prescription}/download', [PortalController::class, 'downloadPrescription'])->name('prescriptions.download');
        Route::get('/certificates', [PortalController::class, 'medicalCertificates'])->name('certificates');
        Route::get('/certificates/{certificate}/download', [PortalController::class, 'downloadCertificate'])->name('certificates.download');
    });

    Route::get('/export-laudo', [PortalController::class, 'exportLaudo'])->name('export-laudo')->middleware('premium');
    Route::get('/access-logs', [PortalController::class, 'accessLogs'])->name('access-logs')->middleware('premium');

    // Encontrar Profissionais
    Route::prefix('professionals')->name('professionals.')->group(function () {
        Route::get('/search', [ProfessionalSearchController::class, 'index'])->name('search');
        Route::get('/{professional}', [ProfessionalSearchController::class, 'show'])->name('show');
        Route::get('/{professional}/slots', [\App\Http\Controllers\AgendaController::class, 'getSlots'])->name('slots');
        Route::post('/{professional}/schedule', [ProfessionalSearchController::class, 'schedule'])->name('schedule');
        Route::post('/{professional}/request-link', [ProfessionalSearchController::class, 'requestLink'])->name('request-link');
    });

    // Módulo de Relatórios (Monetizado)
    Route::prefix('reports')->name('reports.')->middleware('premium')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/view/{type}', [ReportController::class, 'show'])->name('show');
    });

    // Financeiro / Assinatura
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\SubscriptionController::class, 'index'])->name('index');
        Route::get('/plans', [\App\Http\Controllers\Student\SubscriptionController::class, 'plans'])->name('plans');
        Route::get('/checkout/{plan}', [\App\Http\Controllers\Student\SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/process', [\App\Http\Controllers\Student\SubscriptionController::class, 'processPayment'])->name('process');
        
        Route::post('/update-payment', [\App\Http\Controllers\Student\SubscriptionController::class, 'updatePaymentMethod'])->name('update-payment');
        Route::post('/change-plan', [\App\Http\Controllers\Student\SubscriptionController::class, 'changePlan'])->name('change-plan');
        Route::post('/cancel', [\App\Http\Controllers\Student\SubscriptionController::class, 'cancel'])->name('cancel');
    });
});
