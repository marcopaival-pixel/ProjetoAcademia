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
    Route::get('/treatment-plan', [PortalController::class, 'treatmentPlan'])->name('treatment-plan');
    Route::get('/evolution', [PortalController::class, 'evolution'])->name('evolution');
    Route::get('/prescriptions', [PortalController::class, 'prescriptions'])->name('prescriptions');
    Route::get('/documents', [PortalController::class, 'documents'])->name('documents');
    Route::get('/agenda', [PortalController::class, 'agenda'])->name('agenda');
    Route::get('/messages', [PortalController::class, 'messages'])->name('messages');
    Route::get('/medical-records', [PortalController::class, 'medicalRecords'])->name('medical-records.index');
    Route::get('/medical-records/evolutions', [PortalController::class, 'medicalEvolutions'])->name('medical-records.evolutions');
    Route::get('/medical-records/reports', [PortalController::class, 'medicalReports'])->name('medical-records.reports');
    Route::get('/medical-records/reports/{report}/download', [PortalController::class, 'downloadReport'])->name('medical-records.reports.download');
    Route::get('/medical-records/prescriptions', [PortalController::class, 'medicalPrescriptions'])->name('medical-records.prescriptions');
    Route::get('/medical-records/certificates', [PortalController::class, 'medicalCertificates'])->name('medical-records.certificates');
    Route::get('/medical-records/certificates/{certificate}/download', [PortalController::class, 'downloadCertificate'])->name('medical-records.certificates.download');
    Route::get('/export-laudo', [PortalController::class, 'exportLaudo'])->name('export-laudo');
    Route::get('/access-logs', [PortalController::class, 'accessLogs'])->name('access-logs');

    // Encontrar Profissionais
    Route::prefix('professionals')->name('professionals.')->group(function () {
        Route::get('/search', [ProfessionalSearchController::class, 'index'])->name('search');
        Route::get('/{professional}', [ProfessionalSearchController::class, 'show'])->name('show');
        Route::get('/{professional}/slots', [\App\Http\Controllers\AgendaController::class, 'getSlots'])->name('slots');
        Route::post('/{professional}/schedule', [ProfessionalSearchController::class, 'schedule'])->name('schedule');
        Route::post('/{professional}/request-link', [ProfessionalSearchController::class, 'requestLink'])->name('request-link');
    });

    // Módulo de Relatórios (Monetizado)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/view/{type}', [ReportController::class, 'show'])->name('show');
    });

    // Financeiro / Assinatura
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\SubscriptionController::class, 'index'])->name('index');
        Route::post('/update-payment', [\App\Http\Controllers\Student\SubscriptionController::class, 'updatePaymentMethod'])->name('update-payment');
        Route::post('/change-plan', [\App\Http\Controllers\Student\SubscriptionController::class, 'changePlan'])->name('change-plan');
        Route::post('/cancel', [\App\Http\Controllers\Student\SubscriptionController::class, 'cancel'])->name('cancel');
    });
});
