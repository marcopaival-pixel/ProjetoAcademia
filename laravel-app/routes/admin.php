<?php

use App\Http\Controllers\Admin\AcademyCompanyController;
use App\Http\Controllers\Admin\AdminAreaController;
use App\Http\Controllers\Admin\ApiIntegrationController;
use App\Http\Controllers\Admin\CommercialDashboardController;
use App\Http\Controllers\Admin\CommercialProposalController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerSuccessController;
use App\Http\Controllers\Admin\EmailConfigController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\GoalController;
use App\Http\Controllers\Admin\GroupAdminController;
use App\Http\Controllers\Admin\HistoricoPdfController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\PdfDocumentGeneratorController;
use App\Http\Controllers\Admin\PdfGenerationLogController;
use App\Http\Controllers\Admin\PdfSignatureController;
use App\Http\Controllers\Admin\PdfSuiteController;
use App\Http\Controllers\Admin\PdfTemplateController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\RegistrationApprovalController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoleMenuPermissionController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\Admin\EspecialidadeController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\TenantBackupController;
use App\Http\Controllers\Admin\BulkImportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\RepresentativeAdminController;
use App\Http\Controllers\OmniChatController;
use Illuminate\Support\Facades\Route;

// Painel Administrativo
Route::prefix('admin')->group(function () {
    
    // Login Administrativo
    Route::get('/login', [AdminAreaController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'authenticate'])
        ->middleware('throttle:5,1')
        ->name('admin.login.submit');
    Route::post('/logout', [AdminAreaController::class, 'logout'])->name('admin.logout');

    // Área Protegida Admin
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [AdminAreaController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminAreaController::class, 'users'])->name('admin.users');
        Route::get('/users/create', [AdminAreaController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users/store', [AdminAreaController::class, 'storeUser'])->name('admin.users.store');
        Route::post('/users/{user:id}/toggle-status', [AdminAreaController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
        Route::get('/users/{user:id}/edit', [AdminAreaController::class, 'editUser'])->name('admin.users.edit');
        Route::post('/users/{user}/update', [AdminAreaController::class, 'updateUser'])->name('admin.users.update');
        Route::get('/users/{user}/update', fn($user) => redirect()->route('admin.users.edit', $user));
        Route::delete('/users/{user}', [AdminAreaController::class, 'destroyUser'])->name('admin.users.destroy');
        Route::get('/lgpd/export-user/{user}', [AdminAreaController::class, 'exportUserFullData'])->name('admin.lgpd.export-user');
        Route::post('/users/{user}/reset-password', [AdminAreaController::class, 'resetUserPassword'])->name('admin.security.reset-password');
        Route::post('/users/{user}/send-reset-link', [AdminAreaController::class, 'sendResetEmail'])->name('admin.security.send-reset-link');
        Route::post('/users/{user}/resend-verification', [AdminAreaController::class, 'resendVerificationEmail'])->name('admin.users.resend-verification');

        // RBAC & Permissões
        Route::get('/roles/permissions', [RoleMenuPermissionController::class, 'edit'])->name('admin.settings.permissions.menus');
        Route::post('/roles/permissions', [RoleMenuPermissionController::class, 'update'])->name('admin.settings.permissions.menus.update');
        Route::redirect('/roles/permissions/update', '/admin/roles/permissions', 301);

        Route::prefix('roles')->name('admin.roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::post('/{role}/update', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('delete');
        });

        // Aprovação de Cadastro
        Route::get('/registrations', [RegistrationApprovalController::class, 'index'])->name('admin.registrations.pending');
        Route::post('/registrations/{user}/approve', [RegistrationApprovalController::class, 'approve'])->name('admin.registrations.approve');
        Route::post('/registrations/{user}/reject', [RegistrationApprovalController::class, 'reject'])->name('admin.registrations.reject');

        // Configurações E-mail (Modularizado)
        Route::prefix('settings/email')->name('admin.settings.email.')->group(function () {
            Route::get('/providers', [EmailConfigController::class, 'index'])->name('providers');
            Route::get('/providers/{academyCompany}/edit', [EmailConfigController::class, 'edit'])->name('providers.edit');
            Route::post('/providers/{academyCompany}/update', [EmailConfigController::class, 'update'])->name('providers.update');
            Route::post('/test', [EmailConfigController::class, 'test'])->name('test');
            
            Route::get('/templates', [EmailTemplateController::class, 'index'])->name('templates.index');
            Route::get('/templates/create', [EmailTemplateController::class, 'create'])->name('templates.create');
            Route::post('/templates', [EmailTemplateController::class, 'store'])->name('templates.store');
            Route::get('/templates/{template}/edit', [EmailTemplateController::class, 'edit'])->name('templates.edit');
            Route::post('/templates/{template}/update', [EmailTemplateController::class, 'update'])->name('templates.update');
            
            Route::get('/logs', [EmailLogController::class, 'index'])->name('logs');
        });

        // Configurações Financeiras (Gateways)
        Route::get('/payment-settings', [PaymentSettingController::class, 'index'])->name('admin.settings.payments');
        Route::post('/payment-settings', [PaymentSettingController::class, 'store'])->name('admin.settings.payments.store');
        Route::post('/payment-settings/test', [PaymentSettingController::class, 'testConnection'])->name('admin.settings.payments.test');
        Route::post('/payment-settings/toggle-global', [PaymentSettingController::class, 'toggleGlobal'])->name('admin.settings.payments.toggle-global');

        // Gestão de Planos
        Route::prefix('plans')->name('admin.plans.')->group(function () {
            Route::get('/', [PlanController::class, 'index'])->name('index');
            Route::get('/create', [PlanController::class, 'create'])->name('create');
            Route::post('/store', [PlanController::class, 'store'])->name('store');
            Route::get('/{plan}/edit', [PlanController::class, 'edit'])->name('edit');
            Route::post('/{plan}/update', [PlanController::class, 'update'])->name('update');
            Route::post('/{plan}/toggle-status', [PlanController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Gestão de Cobrança e Créditos (Novo)
        Route::prefix('billing')->name('admin.billing.')->group(function () {
            Route::get('/credits', [\App\Http\Controllers\Admin\BillingController::class, 'index'])->name('credits');
            Route::post('/settings', [\App\Http\Controllers\Admin\BillingController::class, 'updateSettings'])->name('settings.update');
            Route::post('/packages', [\App\Http\Controllers\Admin\BillingController::class, 'storePackage'])->name('packages.store');
            Route::put('/packages/{package}', [\App\Http\Controllers\Admin\BillingController::class, 'updatePackage'])->name('packages.update');
            Route::delete('/packages/{package}', [\App\Http\Controllers\Admin\BillingController::class, 'deletePackage'])->name('packages.delete');
        });

        // Dashboard e Gestão Financeira
        Route::prefix('financial')->name('admin.financial.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\FinancialDashboardController::class, 'index'])->name('dashboard');
            Route::get('/', [\App\Http\Controllers\Admin\FinancialDashboardController::class, 'management'])->name('management');
            Route::get('/reports', [\App\Http\Controllers\Admin\FinancialDashboardController::class, 'reports'])->name('reports');
            Route::post('/actions/{subscription}/{action}', [\App\Http\Controllers\Admin\FinancialDashboardController::class, 'processAction'])->name('actions');
            
            // Consumo de IA
            Route::prefix('ai-credits')->name('ai-credits.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\AiCreditDashboardController::class, 'index'])->name('dashboard');
                Route::get('/report', [\App\Http\Controllers\Admin\AiCreditDashboardController::class, 'report'])->name('report');
            });
        });

        // Catálogo de Exercícios e Avisos
        Route::get('/exercises', [AdminAreaController::class, 'catalog'])->name('admin.exercises.catalog');
        Route::post('/exercises/store', [AdminAreaController::class, 'storeExercise'])->name('admin.exercises.store');
        Route::get('/exercises/{exercise}/edit', [AdminAreaController::class, 'editExercise'])->name('admin.exercises.edit');
        Route::post('/exercises/{exercise}/update', [AdminAreaController::class, 'updateExercise'])->name('admin.exercises.update');
        Route::delete('/exercises/{exercise}/delete', [AdminAreaController::class, 'deleteExercise'])->name('admin.exercises.delete');
        Route::get('/muscles/search', [AdminAreaController::class, 'searchMuscles'])->name('admin.muscles.search');
        
        // Gestão de Músculos
        Route::prefix('muscles')->name('admin.muscles.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MuscleController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\MuscleController::class, 'store'])->name('store');
            Route::post('/group', [App\Http\Controllers\Admin\MuscleController::class, 'storeGroup'])->name('group.store');
            Route::put('/{muscle}', [App\Http\Controllers\Admin\MuscleController::class, 'update'])->name('update');
            Route::delete('/{muscle}', [App\Http\Controllers\Admin\MuscleController::class, 'destroy'])->name('destroy');
        });
        
        Route::get('/announcements', [AdminAreaController::class, 'announcements'])->name('admin.announcements');
        Route::post('/announcements/store', [AdminAreaController::class, 'storeAnnouncement'])->name('admin.announcements.store');
        Route::post('/announcements/{announcement}/delete', [AdminAreaController::class, 'deleteAnnouncement'])->name('admin.announcements.delete');

        // Gestão de Representantes
        Route::prefix('representatives')->name('admin.representatives.')->group(function () {
            Route::get('/', [RepresentativeAdminController::class, 'index'])->name('index');
            Route::post('/{user}/approve', [RepresentativeAdminController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [RepresentativeAdminController::class, 'reject'])->name('reject');
            Route::get('/withdrawals', [RepresentativeAdminController::class, 'withdrawals'])->name('withdrawals');
            Route::post('/withdrawals/{withdrawal}/update', [RepresentativeAdminController::class, 'updateWithdrawal'])->name('withdrawals.update');
        });

        // Monitoramento e Auditoria (LGPD/Erros/Segurança)
        Route::get('/lgpd', [AdminAreaController::class, 'lgpdDashboard'])->name('admin.lgpd.index');
        Route::get('/lgpd/consents', [AdminAreaController::class, 'consents'])->name('admin.lgpd.consents');
        Route::get('/lgpd/incidents', [AdminAreaController::class, 'incidents'])->name('admin.lgpd.incidents');
        Route::post('/lgpd/incidents', [AdminAreaController::class, 'storeIncident'])->name('admin.lgpd.incidents.store');
        Route::get('/security', [AdminAreaController::class, 'security'])->name('admin.security.index');
        Route::post('/security/change-password', [AdminAreaController::class, 'changeAdminPassword'])->name('admin.security.change-password');
        Route::get('/system-errors', [AdminAreaController::class, 'systemErrors'])->name('admin.system-errors');
        Route::post('/system-errors/clear', [AdminAreaController::class, 'clearErrors'])->name('admin.system-errors.clear');
        
        // Configurações Globais e Monitoramento
        Route::get('/settings', [AdminAreaController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminAreaController::class, 'saveSettings'])->name('admin.settings.store');
        Route::post('/settings/test-email', [AdminAreaController::class, 'testEmail'])->name('admin.settings.test');
        Route::get('/monitoring', [AdminAreaController::class, 'monitoring'])->name('admin.monitoring');
        Route::get('/ai-monitoring', [AdminAreaController::class, 'aiMonitoring'])->name('admin.ai.monitoring');
        
        // Controle Operacional, Resiliência e Manutenção
        Route::prefix('operations')->name('admin.operations.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OperationsDashboardController::class, 'index'])->name('index');
            Route::post('/update', [\App\Http\Controllers\Admin\OperationsDashboardController::class, 'update'])->name('update');
        });
        
        // Gestão de Backups
        Route::prefix('backups')->name('admin.backups.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/create', [BackupController::class, 'create'])->name('create');
            Route::get('/download/{disk}/{fileName}', [BackupController::class, 'download'])->name('download');
            Route::delete('/{disk}/{fileName}', [BackupController::class, 'delete'])->name('delete');
            Route::post('/restore', [BackupController::class, 'restore'])->name('restore');
            
            // Backup por Empresa (Tenant)
            Route::prefix('tenant/{companyId}')->name('tenant.')->group(function () {
                Route::get('/', [TenantBackupController::class, 'index'])->name('index');
                Route::post('/create', [TenantBackupController::class, 'create'])->name('create');
                Route::get('/download/{fileName}', [TenantBackupController::class, 'download'])->name('download');
                Route::delete('/{fileName}', [TenantBackupController::class, 'delete'])->name('delete');
                Route::post('/restore', [TenantBackupController::class, 'restore'])->name('restore');
            });
        });
        
        // Exportação
        Route::get('/export/users', [AdminAreaController::class, 'exportUsersCsv'])->name('admin.export.users');
        Route::get('/export/payments', [AdminAreaController::class, 'exportPaymentsCsv'])->name('admin.export.payments');

        // OmniChannel Agent Panel
        Route::prefix('omnichannel')->group(function () {
            Route::get('/', fn() => view('admin.omnichannel'))->name('admin.omnichannel');
            Route::get('/bots', [OmniChatController::class, 'bots'])->name('admin.omnichannel.bots');
            Route::prefix('api')->group(function () {
                Route::get('/conversations', [OmniChatController::class, 'activeConversations'])->name('omni.conversations');
                Route::get('/conversations/{id}/messages', [OmniChatController::class, 'getHistory'])->name('omni.messages');
                Route::post('/conversations/{id}/reply', [OmniChatController::class, 'agentReply'])->name('omni.reply');
            });
        });

        Route::prefix('api-integrations')->name('admin.api-integrations.')->group(function () {
            Route::get('/', [ApiIntegrationController::class, 'index'])->name('index');
            Route::get('/create', [ApiIntegrationController::class, 'create'])->name('create');
            Route::post('/store', [ApiIntegrationController::class, 'store'])->name('store');
            Route::get('/{apiIntegration}/edit', [ApiIntegrationController::class, 'edit'])->name('edit');
            Route::post('/{apiIntegration}/update', [ApiIntegrationController::class, 'update'])->name('update');
            Route::delete('/{apiIntegration}', [ApiIntegrationController::class, 'destroy'])->name('destroy');
            Route::post('/{apiIntegration}/test', [ApiIntegrationController::class, 'testConnection'])->name('test');
            Route::post('/{apiIntegration}/toggle-status', [ApiIntegrationController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Gestão de Cupons
        Route::prefix('coupons')->name('admin.coupons.')->group(function () {
            Route::get('/', [AdminCouponController::class, 'index'])->name('index');
            Route::post('/{coupon}/approve', [AdminCouponController::class, 'approve'])->name('approve');
            Route::post('/{coupon}/reject', [AdminCouponController::class, 'reject'])->name('reject');
        });

        // PDF Suite, Gerador e Histórico
        Route::get('/pdf-suite', [PdfSuiteController::class, 'index'])->name('admin.pdf-suite.index');
        
        Route::get('/pdf-documents/generate', [PdfDocumentGeneratorController::class, 'create'])->name('admin.pdf-documents.generate');
        Route::post('/pdf-documents/preview', [PdfDocumentGeneratorController::class, 'preview'])->name('admin.pdf-documents.preview');
        Route::post('/pdf-documents/download', [PdfDocumentGeneratorController::class, 'download'])->name('admin.pdf-documents.download');
        
        Route::get('/pdf-historico', [HistoricoPdfController::class, 'index'])->name('admin.pdf-historico.index');
        Route::get('/pdf-historico/{historicoPdf}/download', [HistoricoPdfController::class, 'download'])->name('admin.pdf-historico.download');
        Route::post('/pdf-historico/{historicoPdf}/cancel', [HistoricoPdfController::class, 'cancel'])->name('admin.pdf-historico.cancel');
        Route::post('/pdf-historico/{historicoPdf}/resend', [HistoricoPdfController::class, 'resend'])->name('admin.pdf-historico.resend');

        // Academy Companies (Contexto PDF)
        Route::prefix('pdf-companies')->name('admin.pdf-companies.')->group(function () {
            Route::get('/', [AcademyCompanyController::class, 'index'])->name('index');
            Route::get('/create', [AcademyCompanyController::class, 'create'])->name('create');
            Route::post('/store', [AcademyCompanyController::class, 'store'])->name('store');
            Route::get('/{academyCompany}/edit', [AcademyCompanyController::class, 'edit'])->name('edit');
            Route::post('/{academyCompany}/update', [AcademyCompanyController::class, 'update'])->name('update');
            Route::post('/{academyCompany}/unit', [AcademyCompanyController::class, 'storeUnit'])->name('units.store');
        });

        // Impersonação de Clínica (Suporte/Manutenção)
        Route::prefix('impersonate-clinic')->name('admin.impersonate-clinic.')->group(function () {
            Route::get('/{company}/start', [\App\Http\Controllers\Admin\AdminClinicImpersonationController::class, 'start'])->name('start');
            Route::post('/{company}/start', [\App\Http\Controllers\Admin\AdminClinicImpersonationController::class, 'store'])->name('store');
            Route::post('/stop', [\App\Http\Controllers\Admin\AdminClinicImpersonationController::class, 'stop'])->name('stop');
        });

        // Implantação de Clínica (Wizard)
        Route::prefix('clinic-implantation')->name('admin.clinic-onboarding.')->group(function () {
            Route::get('/{company?}', [\App\Http\Controllers\Admin\ClinicImplantationController::class, 'index'])->name('index');
            Route::get('/{company}/step/{step}', [\App\Http\Controllers\Admin\ClinicImplantationController::class, 'showStep'])->name('step');
            Route::post('/{company}/step/{step}', [\App\Http\Controllers\Admin\ClinicImplantationController::class, 'saveStep'])->name('step.save');
        });

        Route::middleware('permission:pdf.templates.manage')->prefix('pdf-templates')->name('admin.pdf-templates.')->group(function () {
            Route::get('/', [PdfTemplateController::class, 'index'])->name('index');
            Route::get('/logs', [PdfGenerationLogController::class, 'index'])->name('logs');
            Route::get('/create', [PdfTemplateController::class, 'create'])->name('create');
            Route::post('/store', [PdfTemplateController::class, 'store'])->name('store');
            Route::get('/{pdfTemplate}/edit', [PdfTemplateController::class, 'edit'])->name('edit');
            Route::post('/{pdfTemplate}/update', [PdfTemplateController::class, 'update'])->name('update');
        });

        // Gestão de Propostas e CRM (Vendas)
        Route::get('/commercial', [CommercialDashboardController::class, 'index'])->name('admin.commercial.dashboard');
        Route::prefix('proposals')->name('admin.proposals.')->group(function () {
            Route::get('/', [CommercialProposalController::class, 'index'])->name('index');
            Route::get('/create', [CommercialProposalController::class, 'create'])->name('create');
            Route::post('/store', [CommercialProposalController::class, 'store'])->name('store');
            Route::get('/{proposal}', [CommercialProposalController::class, 'show'])->name('show');
            Route::get('/{proposal}/edit', [CommercialProposalController::class, 'edit'])->name('edit');
            Route::post('/{proposal}/update', [CommercialProposalController::class, 'update'])->name('update');
            Route::delete('/{proposal}', [CommercialProposalController::class, 'destroy'])->name('destroy');
            Route::post('/{proposal}/send', [CommercialProposalController::class, 'send'])->name('send');
        });
        
        Route::prefix('leads')->name('admin.leads.')->group(function () {
            Route::get('/', [LeadController::class, 'index'])->name('index');
            Route::get('/funnel', [LeadController::class, 'funnel'])->name('funnel');
            Route::get('/create', [LeadController::class, 'create'])->name('create');
            Route::post('/store', [LeadController::class, 'store'])->name('store');
            Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
            Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('edit');
            Route::post('/{lead}/update', [LeadController::class, 'update'])->name('update');
            Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('destroy');
            Route::post('/{lead}/status', [LeadController::class, 'updateStatus'])->name('update-status');
            Route::post('/{lead}/interaction', [LeadController::class, 'storeInteraction'])->name('interaction.store');
            Route::post('/{lead}/demo', [LeadController::class, 'generateDemo'])->name('demo.generate');
        });

        // Metas e Performance
        Route::prefix('goals')->name('admin.goals.')->group(function () {
            Route::get('/', [GoalController::class, 'index'])->name('index');
            Route::post('/', [GoalController::class, 'store'])->name('store');
            Route::delete('/{goal}', [GoalController::class, 'destroy'])->name('destroy');
        });

        // Customer Success & Suporte
        Route::prefix('cs')->name('admin.cs.')->group(function () {
            Route::get('/', [CustomerSuccessController::class, 'index'])->name('index');
            Route::get('/retention', [CustomerSuccessController::class, 'retention'])->name('retention');
        });
        Route::prefix('support')->name('admin.support.')->group(function () {
            Route::get('/', [SupportTicketController::class, 'index'])->name('index');
            Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('show');
            Route::post('/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('reply');
        });

        // Base de Conhecimento (Help Center)
        Route::prefix('kb')->name('admin.kb.')->group(function () {
            Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
            Route::get('/create', [KnowledgeBaseController::class, 'create'])->name('create');
            Route::post('/', [KnowledgeBaseController::class, 'store'])->name('store');
            Route::get('/{knowledgeArticle}/edit', [KnowledgeBaseController::class, 'edit'])->name('edit');
            Route::put('/{knowledgeArticle}', [KnowledgeBaseController::class, 'update'])->name('update');
            Route::delete('/{knowledgeArticle}', [KnowledgeBaseController::class, 'destroy'])->name('destroy');
            
            // Categorias
            Route::post('/category', [KnowledgeBaseController::class, 'storeCategory'])->name('category.store');
            Route::put('/category/{category}', [KnowledgeBaseController::class, 'updateCategory'])->name('category.update');
            Route::delete('/category/{category}', [KnowledgeBaseController::class, 'destroyCategory'])->name('category.destroy');
        });

        // Gestão da Academia (Treinamento)
        Route::prefix('training')->name('admin.training.')->group(function () {
            Route::get('/', [TrainingController::class, 'index'])->name('index');
            Route::post('/modules', [TrainingController::class, 'storeModule'])->name('modules.store');
            Route::post('/lessons', [TrainingController::class, 'storeLesson'])->name('lessons.store');
            Route::delete('/modules/{module}', [TrainingController::class, 'destroyModule'])->name('modules.destroy');
            Route::delete('/lessons/{lesson}', [TrainingController::class, 'destroyLesson'])->name('lessons.destroy');
        });

        // Gestão de Especialidades
        Route::prefix('especialidades')->name('admin.especialidades.')->group(function () {
            Route::get('/', [EspecialidadeController::class, 'index'])->name('index');
            Route::post('/', [EspecialidadeController::class, 'store'])->name('store');
            Route::get('/template', [EspecialidadeController::class, 'downloadTemplate'])->name('template');
            Route::get('/export', [EspecialidadeController::class, 'export'])->name('export');
            Route::post('/import', [EspecialidadeController::class, 'import'])->name('import');
            Route::get('/{especialidade}/edit', [EspecialidadeController::class, 'edit'])->name('edit');
            Route::post('/{especialidade}/update', [EspecialidadeController::class, 'update'])->name('update');
            Route::post('/{especialidade}/toggle-status', [EspecialidadeController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{especialidade}', [EspecialidadeController::class, 'destroy'])->name('destroy');
        });

        // Gestão de Suplementos (Catálogo Global)
        Route::prefix('supplements')->name('admin.supplements.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SupplementController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\SupplementController::class, 'store'])->name('store');
            Route::put('/{supplement}', [App\Http\Controllers\Admin\SupplementController::class, 'update'])->name('update');
            Route::post('/{supplement}/toggle-status', [App\Http\Controllers\Admin\SupplementController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{supplement}', [App\Http\Controllers\Admin\SupplementController::class, 'destroy'])->name('destroy');
        });

        // Gestão de Clínica (Para Gestores e Admin)
        Route::prefix('clinic')->name('admin.clinic.')->group(function () {
            Route::get('/settings', [\App\Http\Controllers\Clinic\ClinicManagementController::class, 'index'])->name('settings');
            Route::post('/settings', [\App\Http\Controllers\Clinic\ClinicManagementController::class, 'updateBranding'])->name('settings.update');
            Route::get('/billing', [\App\Http\Controllers\Clinic\BillingController::class, 'index'])->name('billing');
            
            // Protocolos Padrão da Clínica
            Route::resource('protocols', \App\Http\Controllers\Clinic\ProtocolController::class);
        });

        // Importação em Massa
        Route::prefix('import')->name('admin.import.')->group(function () {
            Route::get('/template/{module}', [BulkImportController::class, 'downloadTemplate'])->name('template');
            Route::post('/{module}', [BulkImportController::class, 'import'])->name('submit');
        });

        // Novo Sistema de Cadastros Estruturados
        Route::prefix('registrations')->name('admin.registrations.')->group(function () {
            Route::get('/menu', [\App\Http\Controllers\Admin\RegistrationController::class, 'index'])->name('index');
            
            // Profissional Único
            Route::get('/professional-unico', [\App\Http\Controllers\Admin\RegistrationController::class, 'createProfessionalUnico'])->name('professional-unico');
            Route::post('/professional-unico', [\App\Http\Controllers\Admin\RegistrationController::class, 'storeProfessionalUnico'])->name('professional-unico.store');
            
            // Profissional Clínica
            Route::get('/professional-clinica', [\App\Http\Controllers\Admin\RegistrationController::class, 'createProfessionalClinica'])->name('professional-clinica');
            Route::post('/professional-clinica', [\App\Http\Controllers\Admin\RegistrationController::class, 'storeProfessionalClinica'])->name('professional-clinica.store');
            
            // Funcionário Clínica
            Route::get('/funcionario-clinica', [\App\Http\Controllers\Admin\RegistrationController::class, 'createFuncionarioClinica'])->name('funcionario-clinica');
            Route::post('/funcionario-clinica', [\App\Http\Controllers\Admin\RegistrationController::class, 'storeFuncionarioClinica'])->name('funcionario-clinica.store');
            
            // Paciente Profissional Único
            Route::get('/paciente-profissional', [\App\Http\Controllers\Admin\RegistrationController::class, 'createPacienteProfissional'])->name('paciente-profissional');
            Route::post('/paciente-profissional', [\App\Http\Controllers\Admin\RegistrationController::class, 'storePacienteProfissional'])->name('paciente-profissional.store');
            
            // Paciente Clínica
            Route::get('/paciente-clinica', [\App\Http\Controllers\Admin\RegistrationController::class, 'createPacienteClinica'])->name('paciente-clinica');
            Route::post('/paciente-clinica', [\App\Http\Controllers\Admin\RegistrationController::class, 'storePacienteClinica'])->name('paciente-clinica.store');

            // Funcionalidade de Vínculo de Profissionais a Paciente
            Route::get('/paciente/{user}/vincular', [\App\Http\Controllers\Admin\RegistrationController::class, 'vincularProfissional'])->name('paciente.vincular');
            Route::post('/paciente/{user}/vincular', [\App\Http\Controllers\Admin\RegistrationController::class, 'storeVinculo'])->name('paciente.vincular.store');
            Route::delete('/paciente/{user}/vincular/{professional}', [\App\Http\Controllers\Admin\RegistrationController::class, 'removeVinculo'])->name('paciente.vincular.remove');
        });
    });
});
