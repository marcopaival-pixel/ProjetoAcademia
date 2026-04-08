<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FoodLookupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MonthlyReportPdfController;
use App\Http\Controllers\MercadoPago\CheckoutStartController;
use App\Http\Controllers\MercadoPago\ReturnController as MpReturnController;
use App\Http\Controllers\MercadoPago\SubReturnController;
use App\Http\Controllers\MercadoPago\WebhookController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\Admin\UserDirectoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WeightController;
use App\Models\Conversation;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InternalEmailController;
use App\Http\Controllers\BodyAnalysisController;
use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\LoadProgressionController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\NutritionController;
use App\Http\Controllers\Professional\DashboardController as ProfessionalDashboardController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ActiveRestController;
use App\Http\Controllers\HydrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/fix-db', function () {
    $output = [];

    // ── Schema sync ──────────────────────────────────────────────
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('internal_emails')) {
            \Illuminate\Support\Facades\Schema::create('internal_emails', function ($table) {
                $table->id();
                $table->unsignedInteger('remetente_id');
                $table->unsignedInteger('destinatario_id');
                $table->string('assunto', 200);
                $table->text('mensagem');
                $table->boolean('lida')->default(false);
                $table->timestamp('data_envio')->nullable();
                $table->timestamp('data_leitura')->nullable();
                $table->timestamp('excluded_at_sender')->nullable();
                $table->timestamp('excluded_at_receiver')->nullable();
                $table->enum('status', ['draft', 'outbox', 'sent', 'failed'])->default('sent');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('water_entries', 'drank_at')) {
            \Illuminate\Support\Facades\Schema::table('water_entries', function ($table) {
                $table->timestamp('drank_at')->nullable();
                $table->string('source')->nullable();
            });
        }
        $output[] = '✅ Schema OK';
    } catch (\Exception $e) {
        $output[] = '❌ Schema: ' . $e->getMessage();
    }

    // ── Body images copy ─────────────────────────────────────────
    $src = 'C:/Users/paiva/.gemini/antigravity/brain/5d69c1a7-3818-40c0-9e37-4d2d45b59bf0/';
    $dst = public_path('images/body/');
    if (!is_dir($dst)) mkdir($dst, 0755, true);

    $files = [
        'body_female_front_1775673375488.png' => 'female_front.png',
        'body_female_back_1775673389987.png'  => 'female_back.png',
        'body_male_front_1775673405350.png'   => 'male_front.png',
        'body_male_back_1775673421690.png'    => 'male_back.png',
    ];
    foreach ($files as $from => $to) {
        $s = $src . $from;
        $t = $dst . $to;
        if (file_exists($t)) {
            $output[] = "🟡 Imagem já existe: {$to}";
        } elseif (file_exists($s) && copy($s, $t)) {
            $output[] = '✅ Imagem copiada: ' . $to . ' (' . round(filesize($t)/1024) . ' KB)';
        } else {
            $output[] = "❌ Falha/não encontrado: {$from}";
        }
    }

    return response('<pre style="font-family:monospace;background:#0b0e14;color:#e2e8f0;padding:2rem;">'
        . implode("\n", $output)
        . "\n\n<a href='/progression/plans/target-selection' style='color:#60a5fa;'>→ Ir para a página</a>"
        . '</pre>');
});

// Rota temporária para copiar imagens do corpo (remover após usar)
Route::get('/setup-body-images', function () {
    $src  = 'C:/Users/paiva/.gemini/antigravity/brain/5d69c1a7-3818-40c0-9e37-4d2d45b59bf0/';
    $dst  = public_path('images/body/');

    if (!is_dir($dst)) {
        mkdir($dst, 0755, true);
    }

    $files = [
        'body_female_front_1775673375488.png' => 'female_front.png',
        'body_female_back_1775673389987.png'  => 'female_back.png',
        'body_male_front_1775673405350.png'   => 'male_front.png',
        'body_male_back_1775673421690.png'    => 'male_back.png',
    ];

    $html = '<html><head><meta charset="utf-8"></head><body style="font-family:monospace;background:#0b0e14;color:#e2e8f0;padding:2rem;">';
    $html .= '<h2 style="color:#60a5fa;">🖼️ Setup: Body Images</h2>';

    foreach ($files as $from => $to) {
        $source = $src . $from;
        $target = $dst . $to;
        if (file_exists($source)) {
            if (copy($source, $target)) {
                $html .= "<p style='color:#34d399;'>✅ Copiado: <b>{$to}</b> (" . round(filesize($target)/1024) . " KB)</p>";
            } else {
                $html .= "<p style='color:#f87171;'>❌ Falha ao copiar: <b>{$to}</b></p>";
            }
        } else {
            $html .= "<p style='color:#fbbf24;'>⚠️ Fonte não encontrada: <b>{$from}</b></p>";
        }
    }

    $html .= '<br><hr style="border-color:#1e293b;">';
    $html .= '<p style="color:#94a3b8;">Concluído! <a href="/progression/plans/target-selection" style="color:#60a5fa;">→ Ir para a página</a></p>';
    $html .= '</body></html>';

    return response($html);
});


// Rotas Legado PHP
Route::post('/mp_webhook.php', WebhookController::class);


Route::get('/set_theme.php', fn () => redirect('/'));

Route::get('/logout.php', function (Request $request) {
    if (Auth::check()) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return redirect('/');
});

Route::get('/index.php', function () {
    return redirect(auth()->check() ? '/dashboard' : '/', 301);
});

$legacyGetRedirects = [
    'login' => '/login',
    'register' => '/register',
    'dashboard' => '/dashboard',
    'diary' => '/diary',
    'exercise' => '/exercise',
    'weight' => '/weight',
    'report' => '/report',
    'export' => '/export',
    'plano' => '/plano',
    'mp_return' => '/mp/return',
    'mp_sub_return' => '/mp/sub-return',
];

foreach ($legacyGetRedirects as $file => $target) {
    Route::get($file.'.php', function (Request $request) use ($target) {
        $q = $request->getQueryString();

        return redirect($target.($q !== null && $q !== '' ? '?'.$q : ''), 301);
    });
}

Route::middleware('auth')->group(function () {
    Route::get('/profile.php', fn () => redirect('/profile', 301));
    Route::get('/mp_start.php', fn () => redirect()->route('plano', [], 301));
    Route::post('/mp_start.php', CheckoutStartController::class);

    Route::match(['get', 'post'], '/dashboard.php', [DashboardController::class, 'show']);
    Route::match(['get', 'post'], '/diary.php', [DiaryController::class, 'index']);
    Route::match(['get', 'post'], '/exercise.php', [ExerciseController::class, 'index']);
    Route::match(['get', 'post'], '/weight.php', WeightController::class);
});

Route::get('/', HomeController::class)->name('home');

// Onboarding Wizard
Route::prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/welcome', [OnboardingController::class, 'welcome'])->name('welcome');
    Route::get('/step1', [OnboardingController::class, 'step1'])->name('step1');
    Route::post('/step1', [OnboardingController::class, 'saveStep1'])->name('step1.save');
    Route::get('/step2', [OnboardingController::class, 'step2'])->name('step2');
    Route::get('/goal', fn() => redirect()->route('onboarding.welcome')); // Alias para onboarding/welcome
    Route::post('/step2', [OnboardingController::class, 'saveStep2'])->name('step2.save');
    
    Route::get('/step2/feedback', [OnboardingController::class, 'step2Feedback'])->name('step2.feedback');
    Route::get('/step2/obstacles', [OnboardingController::class, 'step2Obstacles'])->name('step2.obstacles');
    Route::post('/step2/obstacles', [OnboardingController::class, 'saveStep2Obstacles'])->name('step2.obstacles.save');
    Route::get('/step2/understanding', [OnboardingController::class, 'step2Understanding'])->name('step2.understanding');

    Route::get('/step3', [OnboardingController::class, 'step3'])->name('step3');
    Route::get('/activity', fn() => redirect()->route('onboarding.step3')); 
    Route::post('/step3', [OnboardingController::class, 'saveStep3'])->name('step3.save');

    Route::get('/step4', [OnboardingController::class, 'step4'])->name('step4');
    Route::get('/personal-info', fn() => redirect()->route('onboarding.step4'));
    Route::post('/step4', [OnboardingController::class, 'saveStep4'])->name('step4.save');

    Route::get('/step5', [OnboardingController::class, 'step5'])->name('step5');
    Route::get('/specs', fn() => redirect()->route('onboarding.step5'));
    Route::post('/step5', [OnboardingController::class, 'saveStep5'])->name('step5.save');

    Route::get('/step6', [OnboardingController::class, 'step6'])->name('step6');
    Route::get('/weekly-goal', fn() => redirect()->route('onboarding.step6'));
    Route::post('/step6', [OnboardingController::class, 'saveStep6'])->name('step6.save');

    Route::get('/step7', [OnboardingController::class, 'step7'])->name('step7');
    Route::get('/account', fn() => redirect()->route('onboarding.step7'));
    Route::post('/step7', [OnboardingController::class, 'saveStep7'])->name('step7.save');
    Route::get('/step8', [OnboardingController::class, 'step8'])->name('step8');
    Route::post('/step8', [OnboardingController::class, 'saveStep8'])->name('step8.save');

    Route::get('/finish', [OnboardingController::class, 'finish'])->name('finish');
});

// LGPD Public Legal Pages
Route::get('/legal/privacy-policy', [PrivacyController::class, 'privacyPolicy'])->name('legal.privacy');
Route::get('/legal/terms-of-use', [PrivacyController::class, 'termsOfUse'])->name('legal.terms');
Route::get('/legal/cookies', [PrivacyController::class, 'cookiePolicy'])->name('legal.cookies');

Route::post('/theme', ThemeController::class)->name('theme');

Route::post('/mp/webhook', WebhookController::class);

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::match(['get', 'post'], '/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    Route::post('/profile.php', [ProfileController::class, 'update']);

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update']);
    
    // LGPD - Data Rights
    Route::get('/privacy/download-data', [PrivacyController::class, 'downloadMyData'])->name('privacy.download');
    Route::post('/privacy/request-deletion', [PrivacyController::class, 'requestAccountDeletion'])->name('privacy.request-deletion');
    Route::post('/privacy/accept-consent', [PrivacyController::class, 'acceptConsent'])->name('privacy.accept-consent');

    Route::get('/nutrition', [NutritionController::class, 'index'])->name('nutrition.index');
    Route::post('/nutrition/goal', [NutritionController::class, 'updateGoal'])->name('nutrition.update-goal');

    // Avaliação Física (Medidas e BF)
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');

    // Ranking Geral (Gamificação)
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Descanso Ativo (Mobilidade e Recuperação)
    Route::get('/active-rest', [ActiveRestController::class, 'index'])->name('active-rest.index');
    Route::match(['get', 'post'], '/diary', [DiaryController::class, 'index'])->name('diary');
    Route::match(['get', 'post'], '/exercise', [ExerciseController::class, 'index'])->name('exercise');
    Route::match(['get', 'post'], '/weight', WeightController::class)->name('weight');
    Route::get('/report', ReportController::class)->name('report');
    Route::get('/report/monthly-pdf', MonthlyReportPdfController::class)->name('report.monthly.pdf');
    Route::get('/export', ExportController::class)->name('export');
    Route::get('/plano', PlanoController::class)->name('plano');
    Route::post('/mp/start', CheckoutStartController::class)->name('mp.start');
    Route::get('/mp/return', MpReturnController::class)->name('mp.return');
    Route::get('/mp/sub-return', SubReturnController::class)->name('mp.sub-return');

    // Mensagens Internas
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/bulk-delete', [MessageController::class, 'bulkDelete'])->name('messages.bulk-delete');

    // Mensagens Internas (Email-like)
    Route::get('/internal-email', [InternalEmailController::class, 'inbox'])->name('internal-email.inbox');
    Route::get('/internal-email/sent', [InternalEmailController::class, 'sent'])->name('internal-email.sent');
    Route::get('/internal-email/outbox', [InternalEmailController::class, 'outbox'])->name('internal-email.outbox');
    Route::get('/internal-email/trash', [InternalEmailController::class, 'trash'])->name('internal-email.trash');
    Route::get('/internal-email/create', [InternalEmailController::class, 'create'])->name('internal-email.create');
    Route::post('/internal-email', [InternalEmailController::class, 'store'])->name('internal-email.store');
    Route::get('/internal-email/{message}', [InternalEmailController::class, 'show'])->name('internal-email.show');
    Route::post('/internal-email/{message}/read', [InternalEmailController::class, 'markAsRead'])->name('internal-email.read');
    Route::post('/internal-email/{message}/unread', [InternalEmailController::class, 'markAsUnread'])->name('internal-email.unread');
    Route::post('/internal-email/{message}/restore', [InternalEmailController::class, 'restore'])->name('internal-email.restore');
    Route::get('/api/internal-email/unread-count', [InternalEmailController::class, 'unreadCount'])->name('internal-email.unread-count');
    Route::delete('/internal-email/{message}', [InternalEmailController::class, 'destroy'])->name('internal-email.destroy');
    Route::delete('/internal-email/{message}/permanent', [InternalEmailController::class, 'permanentDelete'])->name('internal-email.permanent');

    // Progressão de Carga
    Route::get('/progression/plans', [TrainingPlanController::class, 'index'])->name('progression.plans.index');
    Route::get('/progression/plans/create', [TrainingPlanController::class, 'create'])->name('progression.plans.create');
    Route::get('/progression/plans/target-selection', [TrainingPlanController::class, 'targetSelection'])->name('progression.plans.target-selection');
    Route::post('/progression/plans/target-selection', [TrainingPlanController::class, 'storeTargetSelection'])->name('progression.plans.store-target-selection');
    Route::post('/progression/plans', [TrainingPlanController::class, 'store'])->name('progression.plans.store');
    Route::get('/progression/plans/{plan}', [TrainingPlanController::class, 'show'])->name('progression.plans.show');
    Route::post('/progression/plans/{plan}/duplicate', [TrainingPlanController::class, 'duplicate'])->name('progression.plans.duplicate');
    Route::get('/progression/plans/{plan}/pdf', [TrainingPlanController::class, 'exportPdf'])->name('progression.plans.pdf');
    
    Route::get('/progression/log/{plan}', [LoadProgressionController::class, 'logSession'])->name('progression.log');
    Route::post('/progression/log', [LoadProgressionController::class, 'storeLog'])->name('progression.log.store');
    Route::get('/progression/charts', [LoadProgressionController::class, 'charts'])->name('progression.charts');

    // Bioimpedância Visual (Cyber-Fit)
    Route::get('/body-analysis', [BodyAnalysisController::class, 'index'])->name('body-analysis.index');
    Route::post('/body-analysis', [BodyAnalysisController::class, 'store'])->name('body-analysis.store');
    Route::get('/body-analysis/compare', [BodyAnalysisController::class, 'compare'])->name('body-analysis.compare');

    // Chat com IA: JSON sob middleware web+auth (sessão e CSRF), não em routes/api.php.
    // O SPA (Vite em outra origem) deve usar VITE_LARAVEL_URL + credentials/cabeçalho CSRF.
    Route::get('/chat', fn() => view('chat-page'))->name('chat.page');

    Route::post('/api/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/api/chat/history', [ChatController::class, 'getHistory'])->name('chat.history');
    Route::post('/api/chat/clear', [ChatController::class, 'clearHistory'])->name('chat.clear');

    // Hidratação (NexHydra)
    Route::get('/hydration', fn() => view('hydration-page'))->name('hydration.index');
    Route::get('/api/hydration/status', [HydrationController::class, 'status']);
    Route::post('/api/hydration/add', [HydrationController::class, 'add']);
    Route::delete('/api/hydration/entry/{entry}', [HydrationController::class, 'destroy']);
    Route::patch('/api/hydration/settings', [HydrationController::class, 'updateSettings']);
    Route::get('/api/hydration/reports', [HydrationController::class, 'reports']);

    Route::get('/professional/dashboard', [ProfessionalDashboardController::class, 'index'])->name('professional.dashboard');
    Route::get('/professional/patients', [\App\Http\Controllers\Professional\PatientController::class, 'index'])->name('professional.patients.index');
    Route::get('/professional/patients/{id}', [\App\Http\Controllers\Professional\PatientController::class, 'show'])->name('professional.patients.show');
    Route::get('/professional/ai-wizard', [\App\Http\Controllers\Professional\AIPrescriptionController::class, 'index'])->name('professional.ai-wizard');
    Route::post('/professional/ai-wizard/generate', [\App\Http\Controllers\Professional\AIPrescriptionController::class, 'generate'])->name('professional.ai-wizard.generate');
    Route::get('/professional/branding', [\App\Http\Controllers\Professional\BrandingController::class, 'index'])->name('professional.branding.index');
    Route::post('/professional/branding', [\App\Http\Controllers\Professional\BrandingController::class, 'update'])->name('professional.branding.update');
    Route::get('/professional/billing', [\App\Http\Controllers\Professional\SubscriptionController::class, 'index'])->name('professional.billing.index');

    Route::get('/my-plan', [\App\Http\Controllers\Patient\PortalController::class, 'index'])->name('patient.portal.index');

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminAreaController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/painel', [\App\Http\Controllers\Admin\AdminAreaController::class, 'dashboard'])->name('painel de controle');
        Route::get('/users', [\App\Http\Controllers\Admin\AdminAreaController::class, 'users'])->name('admin.users');
        Route::get('/logs', [\App\Http\Controllers\Admin\AdminAreaController::class, 'logs'])->name('admin.logs');
        Route::get('/monitoring', [\App\Http\Controllers\Admin\AdminAreaController::class, 'monitoring'])->name('admin.monitoring');
        Route::get('/settings', [\App\Http\Controllers\Admin\AdminAreaController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\AdminAreaController::class, 'saveSettings'])->name('admin.settings.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\AdminAreaController::class, 'editUser'])->name('admin.users.edit');
        Route::post('/users/{user}/update', [\App\Http\Controllers\Admin\AdminAreaController::class, 'updateUser'])->name('admin.users.update');
        Route::get('/exercises', [\App\Http\Controllers\Admin\AdminAreaController::class, 'catalog'])->name('admin.exercises.catalog');
        Route::post('/exercises/store', [\App\Http\Controllers\Admin\AdminAreaController::class, 'storeExercise'])->name('admin.exercises.store');
        Route::get('/exercises/{exercise}/edit', [\App\Http\Controllers\Admin\AdminAreaController::class, 'editExercise'])->name('admin.exercises.edit');
        Route::post('/exercises/{exercise}/update', [\App\Http\Controllers\Admin\AdminAreaController::class, 'updateExercise'])->name('admin.exercises.update');
        Route::delete('/exercises/{exercise}/delete', [\App\Http\Controllers\Admin\AdminAreaController::class, 'deleteExercise'])->name('admin.exercises.delete');
        Route::get('/announcements', [\App\Http\Controllers\Admin\AdminAreaController::class, 'announcements'])->name('admin.announcements');
        Route::post('/announcements/store', [\App\Http\Controllers\Admin\AdminAreaController::class, 'storeAnnouncement'])->name('admin.announcements.store');
        Route::post('/announcements/{announcement}/delete', [\App\Http\Controllers\Admin\AdminAreaController::class, 'deleteAnnouncement'])->name('admin.announcements.delete');
        Route::get('/ai-chat', [\App\Http\Controllers\Admin\AdminAreaController::class, 'aiMonitoring'])->name('admin.ai.monitoring');
        Route::get('/export/users', [\App\Http\Controllers\Admin\AdminAreaController::class, 'exportUsersCsv'])->name('admin.export.users');
        Route::get('/export/payments', [\App\Http\Controllers\Admin\AdminAreaController::class, 'exportPaymentsCsv'])->name('admin.export.payments');

        // LGPD Admin
        Route::get('/lgpd', [\App\Http\Controllers\Admin\AdminAreaController::class, 'lgpdDashboard'])->name('admin.lgpd.index');
        Route::get('/lgpd/consents', [\App\Http\Controllers\Admin\AdminAreaController::class, 'consents'])->name('admin.lgpd.consents');
        Route::get('/lgpd/incidents', [\App\Http\Controllers\Admin\AdminAreaController::class, 'incidents'])->name('admin.lgpd.incidents');
        Route::post('/lgpd/incidents', [\App\Http\Controllers\Admin\AdminAreaController::class, 'storeIncident'])->name('admin.lgpd.incidents.store');
        Route::get('/lgpd/export-user/{user}', [\App\Http\Controllers\Admin\AdminAreaController::class, 'exportUserFullData'])->name('admin.lgpd.export-user');
        Route::get('/system-errors', [\App\Http\Controllers\Admin\AdminAreaController::class, 'systemErrors'])->name('admin.system-errors');
        Route::post('/system-errors/clear', [\App\Http\Controllers\Admin\AdminAreaController::class, 'clearErrors'])->name('admin.system-errors.clear');
    });
});

// Rotas de Autenticação Administrativa
Route::get('/admin/login', [\App\Http\Controllers\Admin\AdminAreaController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Auth\LoginController::class, 'authenticate'])->name('admin.login.submit');
Route::post('/admin/logout', [\App\Http\Controllers\Admin\AdminAreaController::class, 'logout'])->name('admin.logout');

// Rotas públicas para busca de alimentos (sem autenticação, apenas throttle)
Route::middleware('throttle:openfoodfacts')->group(function () {
    Route::get('/api/food/search', [FoodLookupController::class, 'search'])->name('food.search');
    Route::get('/api/food/product/{code}', [FoodLookupController::class, 'product'])->name('food.product');
});
// Rota redundante para compatibilidade com chamadas dinâmicas ao "painel de controle"
Route::get('/painel-admin', [\App\Http\Controllers\Admin\AdminAreaController::class, 'dashboard'])
    ->middleware(['auth', 'admin'])
    ->name('painel de controle');
