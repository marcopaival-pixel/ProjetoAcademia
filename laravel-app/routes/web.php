<?php

use App\Http\Controllers\BodyAnalysisController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\DocumentValidationController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FoodLookupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HydrationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PublicProposalController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NutritionController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Patient\PortalController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Support\TicketController;
use App\Http\Controllers\SystemStatusController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\WeightController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * PROJETO ACADEMIA - ROTAS WEB (ESTRUTURA MODULAR)
 * --------------------------------------------------------------------------
 * As rotas específicas foram movidas para arquivos modulares em routes/:
 * - routes/auth.php          : Autenticação, Registro, Verificação.
 * - routes/admin.php         : Painel Administrativo.
 * - routes/professional.php  : Portal Pro (Profissionais).
 * - routes/patient.php       : Painel do Aluno / Paciente.
 * - routes/mercado_pago.php  : Checkout e Webhooks Financeiros.
 */

// 0. Monitoramento e Saúde
Route::get('/health', [App\Http\Controllers\HealthCheckController::class, 'index'])->name('health.check');

// 1. Home e Páginas Públicas
Route::get('/', HomeController::class)->name('home');

// MODO DEMONSTRAÇÃO
Route::prefix('demo')->name('demo.')->group(function () {
    Route::get('/start', [App\Http\Controllers\DemoController::class, 'start'])->name('start');
    Route::get('/stop', [App\Http\Controllers\DemoController::class, 'stop'])->name('stop');
    Route::post('/reset', [App\Http\Controllers\DemoController::class, 'reset'])->name('reset');
    Route::post('/switch', [App\Http\Controllers\DemoController::class, 'switchProfile'])->name('switch');
});


Route::get(
    '/'.trim((string) config('pdf.validation_path_segment', 'validar-documento'), '/').'/{codigo}',
    [DocumentValidationController::class, 'show']
)->name('documents.validate');

Route::get('/legal/privacy-policy', [PrivacyController::class, 'privacyPolicy'])->name('legal.privacy');
Route::get('/legal/terms-of-use', [PrivacyController::class, 'termsOfUse'])->name('legal.terms');
Route::get('/legal/cookies', [PrivacyController::class, 'cookiePolicy'])->name('legal.cookies');
Route::middleware('auth')->group(function () {
    Route::get('/legal/download-data', [PrivacyController::class, 'downloadMyData'])->name('privacy.download');
    Route::post('/legal/account-deletion', [PrivacyController::class, 'requestAccountDeletion'])->name('privacy.request-deletion');
});

Route::post('/theme', ThemeController::class)->name('theme');

/** Página Meu Plano / Checkout Premium (Acessível publicamente) */
Route::get('/plano', \App\Http\Controllers\PlanoController::class)->name('plano');

// Fluxo de Checkout Modular
Route::get('/checkout/{plan}', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

// Base de conhecimento (Central de Ajuda)
Route::middleware(['auth'])->prefix('kb')->name('kb.')->group(function () {
    Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
    Route::get('/{slug}', [KnowledgeBaseController::class, 'show'])->name('show');
});

// Proposta comercial pública (token)
Route::prefix('proposal')->name('public.proposal.')->group(function () {
    Route::get('/{token}', [PublicProposalController::class, 'show'])->name('show');
    Route::post('/{token}/accept', [PublicProposalController::class, 'accept'])->name('accept');
    Route::post('/{token}/reject', [PublicProposalController::class, 'reject'])->name('reject');
});

// 2. Busca de Alimentos (Throttle)
Route::middleware('throttle:openfoodfacts')->group(function () {
    Route::get('/api/food/search', [FoodLookupController::class, 'search'])->name('food.search');
    Route::get('/api/food/product/{code}', [FoodLookupController::class, 'product'])->name('food.product');
});

// 3. Redirecionamentos de Legados (.php)
Route::get('/index.php', function () {
    return redirect(auth()->check() ? '/dashboard' : '/', 301);
});

$legacyRedirects = [
    'login' => '/login', 'register' => '/register', 'dashboard' => '/dashboard',
    'diary' => '/diary', 'exercise' => '/exercise-log', 'weight' => '/weight',
    'report' => '/report', 'export' => '/export', 'plano' => '/plano',
];
foreach ($legacyRedirects as $file => $target) {
    Route::get($file.'.php', fn(Request $request) => redirect($target . ($request->getQueryString() ? '?'.$request->getQueryString() : ''), 301));
}

// 4. Inclusão de Módulos Específicos
Route::post('/payment/webhook/{gateway}', \App\Http\Controllers\Payment\WebhookController::class)->name('payment.webhook');
require __DIR__.'/auth.php';
require __DIR__.'/mercado_pago.php';
require __DIR__.'/admin.php';
require __DIR__.'/professional.php';
require __DIR__.'/patient.php';
require __DIR__.'/representative.php';

// 5. Marketing Banner API (Público/Autenticado)
Route::post('/api/marketing/banners/{banner}/view', [\App\Http\Controllers\Admin\MarketingBannerController::class, 'trackView'])->name('api.marketing.banners.view');
Route::post('/api/marketing/banners/{banner}/click', [\App\Http\Controllers\Admin\MarketingBannerController::class, 'trackClick'])->name('api.marketing.banners.click');
Route::post('/api/marketing/banners/{banner}/dismiss', [\App\Http\Controllers\Admin\MarketingBannerController::class, 'trackDismiss'])->name('api.marketing.banners.dismiss');

// Legado App Banner
Route::post('/api/marketing/app-banner/lead', [App\Http\Controllers\Admin\AppBannerController::class, 'registerLead'])->name('api.marketing.app-banner.lead');
Route::post('/api/marketing/app-banner/metric', [App\Http\Controllers\Admin\AppBannerController::class, 'trackMetric'])->name('api.marketing.app-banner.metric');

// 6. App Core (Rotas Autenticadas Comuns)
Route::middleware(['auth'])->group(function () {


    // Dashboard e Busca
    // Dashboard e Busca
    Route::middleware('permission:portal.access')->group(function () {
        Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'show'])->name('dashboard');
        Route::get('/global-search', [SearchController::class, 'search'])->name('global.search');
        Route::get('/global-search/suggestions', [SearchController::class, 'suggestions'])->name('global.search.suggestions');
        Route::get('/muscles/search', [App\Http\Controllers\TrainingPlanController::class, 'searchMuscles'])->name('muscles.search');
    });

    // Perfil e Dados (Geral)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::post('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/blocked', [ProfileController::class, 'blockedUsers'])->name('blocked');
        
        // Seleção de Perfil
        Route::get('/selection', [\App\Http\Controllers\Auth\ProfileSelectionController::class, 'index'])->name('selection');
        Route::post('/select', [\App\Http\Controllers\Auth\ProfileSelectionController::class, 'select'])->name('select');
    });

    // Seleção de Clínica (Tenant Context)
    Route::get('/clinic-selection', [\App\Http\Controllers\Auth\ClinicSelectionController::class, 'index'])->name('clinic.selector');
    Route::post('/clinic-select', [\App\Http\Controllers\Auth\ClinicSelectionController::class, 'select'])->name('clinic.select');



    // Hidratação (NexHydra — usado pelo MenuSeeder route hydration.index e pela página hydration-page)
    Route::get('/hydration', [HydrationController::class, 'index'])->name('hydration.index');
    Route::prefix('api/hydration')->group(function () {
        Route::get('/status', [HydrationController::class, 'status']);
        Route::post('/add', [HydrationController::class, 'add']);
        Route::delete('/entry/{entry}', [HydrationController::class, 'destroy']);
        Route::patch('/settings', [HydrationController::class, 'updateSettings']);
        Route::get('/reports', [HydrationController::class, 'reports']);
    });

    // Relatórios e Exportação
    Route::get('/report', ReportController::class)->name('report');
    Route::get('/validate-report', [\App\Http\Controllers\ReportValidationController::class, 'validate'])->name('report.validate');
    Route::get('/export', ExportController::class)->name('export');
    Route::get('/calendar', [App\Http\Controllers\StudentCalendarController::class, 'index'])->name('calendar');

    // Onboarding
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/welcome', [OnboardingController::class, 'welcome'])->name('welcome');
        Route::get('/step1', [OnboardingController::class, 'step1'])->name('step1');
        Route::post('/step1', [OnboardingController::class, 'saveStep1'])->name('step1.save');
        Route::get('/finish', [OnboardingController::class, 'finish'])->name('finish');

        // AJAX Endpoints para o modal de onboarding
        Route::post('/api/update', [OnboardingController::class, 'update'])->name('api.update');
        Route::post('/api/skip', [OnboardingController::class, 'skip'])->name('api.skip');
        Route::post('/api/complete', [OnboardingController::class, 'complete'])->name('api.complete');
    });

    // Novo Onboarding Premium SaaS
    Route::prefix('onboarding-premium')->name('onboarding-premium.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PremiumOnboardingController::class, 'index'])->name('index');
        Route::post('/start', [\App\Http\Controllers\PremiumOnboardingController::class, 'start'])->name('start');
        Route::get('/step/{step}', [\App\Http\Controllers\PremiumOnboardingController::class, 'showStep'])->name('step');
        Route::post('/step/{step}', [\App\Http\Controllers\PremiumOnboardingController::class, 'saveStep'])->name('step.save');
        Route::get('/finish', [\App\Http\Controllers\PremiumOnboardingController::class, 'finish'])->name('finish');
    });

    // IA & NexBot
    Route::middleware('premium')->group(function () {
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.page');
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
            Route::get('/history', [ChatController::class, 'getHistory'])->name('history');
            Route::post('/clear', [ChatController::class, 'clearHistory'])->name('clear');
            Route::post('/execute-action', [ChatController::class, 'executeAction'])->name('execute-action');
            
            // Nova Consulta Inteligente (Biblioteca)
            Route::post('/smart-query', [\App\Http\Controllers\SmartQueryController::class, 'query'])->name('smart-query');
        });
    });

    // Gestão de Créditos de IA
    Route::prefix('ai-credits')->name('ai-credits.')->group(function () {
        Route::get('/packages', [\App\Http\Controllers\AiCreditController::class, 'packages'])->name('packages');
        Route::post('/buy', [\App\Http\Controllers\AiCreditController::class, 'buy'])->name('buy');
        Route::get('/dashboard', [\App\Http\Controllers\AiCreditController::class, 'dashboard'])->name('dashboard');
        Route::post('/distribute', [\App\Http\Controllers\AiCreditController::class, 'distribute'])->name('distribute');
    });

    // Análise Corporal (IA)
    Route::prefix('body-analysis')->name('body-analysis.')->middleware('premium')->group(function () {
        Route::get('/', [BodyAnalysisController::class, 'index'])->name('index');
        Route::post('/store', [BodyAnalysisController::class, 'store'])->name('store');
        Route::get('/compare', [BodyAnalysisController::class, 'compare'])->name('compare');
    });

    // Módulo de Créditos (Geral)
    Route::prefix('credits')->name('credits.')->group(function () {
        Route::get('/buy', [\App\Http\Controllers\CreditoController::class, 'buy'])->name('buy');
        Route::post('/checkout', [\App\Http\Controllers\CreditoController::class, 'checkout'])->name('checkout');
        Route::get('/success/{compra}', [\App\Http\Controllers\CreditoController::class, 'success'])->name('success');
        Route::get('/pending/{compra}', [\App\Http\Controllers\CreditoController::class, 'pending'])->name('pending');
    });

    // Notificações e Mensagens Internas
    Route::get('/notifications/unread-counts', [NotificationController::class, 'unreadCounts'])->name('notifications.unread-counts');
    Route::get('/notifications', [NotificationController::class, 'unreadCounts'])->name('notifications.index');
    Route::prefix('support/tickets')->name('support.tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [TicketController::class, 'reply'])->name('reply');
    });
    Route::get('/system/status', [SystemStatusController::class, 'index'])->name('system.status');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

    require __DIR__.'/features.php';


    
    // Relatórios e Exportação (PDF Mensal)
    Route::get('/report/monthly-pdf', \App\Http\Controllers\MonthlyReportPdfController::class)->name('report.monthly.pdf');

    // Academia NexShape (Treinamento do Cliente)
    Route::prefix('training')->name('training.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Support\TrainingController::class, 'index'])->name('index');
        Route::get('/module/{module}', [\App\Http\Controllers\Support\TrainingController::class, 'showModule'])->name('module');
        Route::get('/module/{module}/lesson/{lesson}', [\App\Http\Controllers\Support\TrainingController::class, 'showLesson'])->name('lesson');
    });
    // Comunidade Social NexShape
    Route::prefix('community')->name('community.')->group(function () {
        Route::get('/', [App\Http\Controllers\CommunityController::class, 'index'])->name('index');
        Route::post('/post', [App\Http\Controllers\CommunityController::class, 'store'])->name('store');
        Route::post('/react/{type}/{id}', [App\Http\Controllers\CommunityController::class, 'react'])->name('react');
        Route::post('/post/{post}/comment', [App\Http\Controllers\CommunityController::class, 'comment'])->name('comment');
    });
});
