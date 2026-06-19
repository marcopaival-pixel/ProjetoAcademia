<?php


use App\Http\Controllers\Patient\PortalController;
use App\Http\Controllers\Patient\ProfessionalSearchController;
use App\Http\Controllers\Patient\ReportController;
use Illuminate\Support\Facades\Route;

// Acesso via Token (Sem middleware auth)
Route::get('/access', [PortalController::class, 'access'])->name('access')->middleware('throttle:5,1');

// Marketplace Público de Profissionais
Route::prefix('patient/professionals')->name('patient.professionals.')->group(function () {
    Route::get('/search', [ProfessionalSearchController::class, 'index'])->name('search');
    Route::get('/{professional}', [ProfessionalSearchController::class, 'show'])->name('show');
    Route::get('/{professional}/slots', [\App\Http\Controllers\AgendaController::class, 'getSlots'])->name('slots');
});

// Seleção de Profissional (Multi-vínculo)
Route::middleware(['auth', 'role:paciente', 'panel.isolation'])->group(function () {
    Route::get('/patient/select-professional', [\App\Http\Controllers\Patient\ProfessionalSelectionController::class, 'index'])->name('patient.professional.selection');
    Route::post('/patient/select-professional', [\App\Http\Controllers\Patient\ProfessionalSelectionController::class, 'select'])->name('patient.professional.select');

    Route::get('/patient/dashboard-choice', [\App\Http\Controllers\Patient\UnifiedDashboardController::class, 'choice'])->name('patient.dashboard.choice');
    Route::get('/patient/unified-dashboard', [\App\Http\Controllers\Patient\UnifiedDashboardController::class, 'index'])->name('patient.unified.dashboard');
});

// Ativação de Conta (Primeiro Acesso - Sem login)
Route::get('/ativar-conta/{token}', [\App\Http\Controllers\Patient\PatientActivationController::class, 'show'])->name('patient.activate.show');
Route::post('/ativar-conta/{token}', [\App\Http\Controllers\Patient\PatientActivationController::class, 'activate'])->name('patient.activate.process');

// Complementação de Dados (Logado mas incompleto)
Route::middleware(['auth', 'role:paciente', 'panel.isolation'])->group(function () {
    Route::get('/patient/complete-profile', [\App\Http\Controllers\Patient\PatientActivationController::class, 'completeProfileShow'])->name('patient.profile.complete');
    Route::post('/patient/complete-profile', [\App\Http\Controllers\Patient\PatientActivationController::class, 'completeProfileStore'])->name('patient.profile.store');
});

// Painel do Aluno / Paciente restricted to auth
Route::middleware([
    'auth',
    'role:paciente',
    'panel.isolation',
    \App\Http\Middleware\EnsurePatientProfileIsComplete::class,
    \App\Http\Middleware\EnsureProfessionalIsSelected::class,
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
    
    Route::prefix('medical-records')->name('medical-records.')->group(function() {
        Route::get('/', [PortalController::class, 'medicalRecords'])->name('index');
        Route::get('/evolutions', [PortalController::class, 'medicalEvolutions'])->name('evolutions');
        Route::get('/reports', [PortalController::class, 'medicalReports'])->name('reports');
        Route::get('/reports/{report}/download', [PortalController::class, 'downloadReport'])->name('reports.download');
        Route::get('/prescriptions', [PortalController::class, 'medicalPrescriptions'])->name('prescriptions');
        Route::get('/prescriptions/{prescription}/download', [PortalController::class, 'downloadPrescription'])->name('prescriptions.download');
        Route::get('/certificates', [PortalController::class, 'medicalCertificates'])->name('certificates');
        Route::get('/certificates/{certificate}/download', [PortalController::class, 'downloadCertificate'])->name('certificates.download');
    });

    Route::get('/export-laudo', [PortalController::class, 'exportLaudo'])->name('export-laudo');
    Route::get('/access-logs', [PortalController::class, 'accessLogs'])->name('access-logs');

    // Gestão de Vínculos e Permissões Multiprofissionais
    Route::prefix('my-professionals')->name('my-professionals.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Patient\ProfessionalManagementController::class, 'index'])->name('index');
        Route::post('/{link}/update-permissions', [\App\Http\Controllers\Patient\ProfessionalManagementController::class, 'updatePermissions'])->name('update-permissions');
        Route::post('/{link}/revoke', [\App\Http\Controllers\Patient\ProfessionalManagementController::class, 'revoke'])->name('revoke');
    });

    // Encontrar Profissionais (Ações restritas)
    Route::prefix('professionals')->name('professionals.')->group(function () {
        Route::post('/{professional}/schedule', [ProfessionalSearchController::class, 'schedule'])->name('schedule');
        Route::post('/{professional}/request-link', [ProfessionalSearchController::class, 'requestLink'])->name('request-link');
    });

});

// Relatórios monetizados — aluno e paciente (sem exigir perfil clínico completo)
Route::middleware([
    'auth',
    'role:aluno|paciente',
    'panel.isolation',
])->prefix('patient/reports')->name('patient.reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/view/{type}', [ReportController::class, 'show'])->name('show');
});

// Financeiro / Assinatura (Liberado para todos os perfis)
Route::middleware([
    'auth',
    'panel.isolation',
])->prefix('patient')->name('patient.')->group(function () {
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
