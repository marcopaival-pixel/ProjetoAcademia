<?php

namespace App\Providers;

use App\Contracts\InvoiceGatewayInterface;
use App\Contracts\PaymentGatewayInterface;
use App\Listeners\MailNotificationAuditListener;
use App\Models\ExerciseEntry;
use App\Models\FiscalSetting;
use App\Models\HealthAlert;
use App\Models\ProfessionalFinanceEntry;
use App\Models\ProfessionalProfile;
use App\Models\User;
use App\Models\WaterEntry;
use App\Observers\ExerciseEntryObserver;
use App\Observers\HealthAlertObserver;
use App\Observers\ProfessionalFinanceEntryObserver;
use App\Observers\WaterEntryObserver;
use App\Policies\FinancialReportPolicy;
use App\Policies\ProfessionalPatientPolicy;
use App\Services\Context\CurrentContext;
use App\Services\DynamicConfigService;
use App\Services\Fiscal\FakeInvoiceGateway;
use App\Services\MailConfigService;
use App\Services\MenuAccessService;
use App\Services\OCR\GoogleVisionOCRService;
use App\Services\OCR\OCRServiceInterface;
use App\Services\Operations\JobMetricsRecorder;
use App\Services\Payment\PaymentGatewayManager;
use App\Support\Theme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;
use Sentry\SentrySdk;
use Sentry\State\Scope;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(CurrentContext::class, function ($app) {
            return new CurrentContext;
        });

        $this->app->singleton(PaymentGatewayManager::class, function ($app) {
            return new PaymentGatewayManager($app);
        });

        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            return $app->make(PaymentGatewayManager::class)->driver();
        });

        $this->app->bind(InvoiceGatewayInterface::class, function () {
            $setting = FiscalSetting::active();
            $provider = $setting?->provider ?: (string) config('fiscal.provider', '');

            if ($provider === '' && app()->environment(['local', 'testing'])) {
                $provider = 'fake';
            }

            if ($provider === 'fake') {
                return new FakeInvoiceGateway;
            }

            throw new \RuntimeException("Gateway fiscal não implementado: {$provider}");
        });

        $this->app->bind(OCRServiceInterface::class, GoogleVisionOCRService::class);
    }

    public function boot(): void
    {
        $financialPolicy = new FinancialReportPolicy;
        Gate::define('admin.financial.dashboard', fn (User $user) => $financialPolicy->viewDashboard($user));
        Gate::define('admin.financial.management', fn (User $user) => $financialPolicy->viewManagement($user));
        Gate::define('admin.financial.reports', fn (User $user) => $financialPolicy->viewReports($user));

        $professionalPatientPolicy = new ProfessionalPatientPolicy;
        Gate::define('professionalPatient.view', fn (User $user, User $patient) => $professionalPatientPolicy->view($user, $patient));
        Gate::define('professionalPatient.update', fn (User $user, User $patient) => $professionalPatientPolicy->update($user, $patient));
        Gate::define('professionalPatient.delete', fn (User $user, User $patient) => $professionalPatientPolicy->delete($user, $patient));

        $bp = (string) config('projeto.base_path');
        if ($bp !== '' && app()->runningInConsole() === false) {
            URL::forceRootUrl(rtrim((string) config('app.url'), '/').$bp);
        }

        // Aplica as configurações de e-mail do banco de dados (fallback global)
        MailConfigService::apply();
        DynamicConfigService::apply();

        Event::listen(NotificationSending::class, function ($event) {
            if ($event->channel === 'mail' && $event->notifiable instanceof User) {
                MailConfigService::apply($event->notifiable->academy_company_id);
            }
        });

        Event::listen(NotificationSent::class, [MailNotificationAuditListener::class, 'handleSent']);
        Event::listen(NotificationFailed::class, [MailNotificationAuditListener::class, 'handleFailed']);

        View::composer('layouts.app', function ($view) {
            $activePatient = null;
            if (auth()->check() && session()->has('active_patient_id')) {
                // Compartilhar apenas os dados básicos para não sobrecarregar
                $activePatient = User::find(session('active_patient_id'));
                if (! $activePatient) {
                    session()->forget('active_patient_id');
                }
            }

            $view->with([
                'projetoTheme' => Theme::current(),
                'themeExplicit' => Theme::isExplicit(),
                'themeNext' => Theme::nextFromRequest(),
                'activePatient' => $activePatient,
            ]);
        });

        View::composer('partials.topbar', function ($view) {
            $user = auth()->user();
            if ($user) {
                $view->with([
                    'aiUsageToday' => $user->getAiCreditsUsedToday(),
                    'aiUsageMonth' => $user->getAiCreditsUsedThisMonth(),
                    'aiUsageTotal' => $user->getAiCreditsUsedTotal(),
                ]);
            }
        });

        View::composer('partials.admin-sidebar', function ($view) {
            $user = auth()->user();
            $map = [];
            if ($user !== null) {
                $map = app(MenuAccessService::class)->getAdminNavVisibilityMap($user);
            }
            $view->with('adminNavVisible', $map);
        });

        View::composer('professional.*', function ($view) {
            if (auth()->check() && auth()->user()->hasRole('professional')) {
                /** @var ProfessionalProfile|null $profile */
                $profile = auth()->user()->professionalProfile;
                if ($profile && $profile->profession) {
                    $profObj = $profile->profession;
                    $professionName = $profObj ? ($profObj->getAttribute('name') ?? 'Geral') : 'Geral';
                } else {
                    $professionName = 'Geral';
                }

                $isFitness = in_array($professionName, ['Educador Físico', 'Personal Trainer']);

                $view->with('patientLabel', $isFitness ? 'Aluno' : 'Paciente');
                $view->with('patientsLabel', $isFitness ? 'Alunos' : 'Pacientes');
            }
        });

        RateLimiter::for('openfoodfacts', function (Request $request) {
            $uid = (int) ($request->user()?->id ?? 0);
            $per = max(5, (int) config('services.openfoodfacts.max_requests_per_minute', 30));

            return Limit::perMinute($per)->by($uid > 0 ? 'off-'.$uid : 'off-ip-'.$request->ip());
        });

        RateLimiter::for('privacy-download', function (Request $request) {
            $uid = (int) ($request->user()?->id ?? 0);

            return Limit::perHour(20)->by($uid > 0 ? 'privacy-u-'.$uid : 'privacy-ip-'.$request->ip());
        });

        RateLimiter::for('marketing-tracking', function (Request $request) {
            return Limit::perMinute(60)->by('mkt-ip-'.$request->ip());
        });

        RateLimiter::for('client-errors', function (Request $request) {
            $limit = max(1, (int) config('observability.client_errors.rate_limit', 10));

            return Limit::perMinute($limit)->by('client-err-ip-'.$request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            $userId = (int) ($request->user()?->id ?? 0);

            return Limit::perMinute(120)->by($userId > 0 ? 'api-u-'.$userId : 'api-ip-'.$request->ip());
        });

        Event::listen(JobProcessed::class, function ($event) {
            $durationMs = 0;
            if (isset($event->job) && method_exists($event->job, 'payload')) {
                $payload = $event->job->payload();
                $pushedAt = $payload['pushedAt'] ?? null;
                if ($pushedAt) {
                    $durationMs = (int) max(0, (microtime(true) - (float) $pushedAt) * 1000);
                }
            }

            JobMetricsRecorder::recordCompleted($durationMs);
        });

        Event::listen(JobFailed::class, function () {
            JobMetricsRecorder::recordFailed();
        });

        if (class_exists(SentrySdk::class) && config('sentry.dsn')) {
            \Sentry\configureScope(function (Scope $scope): void {
                $scope->setTag('app', 'nexshape');
            });
        }

        // Feature and Plan Directives
        Blade::if('feature', function ($key) {
            return auth()->check() && auth()->user()->hasFeature($key);
        });

        Blade::if('planLimit', function ($key, $currentCount) {
            if (! auth()->check()) {
                return false;
            }
            $limit = auth()->user()->getPlanLimit($key);

            return $limit === 0 || $currentCount < $limit;
        });

        Blade::directive('lockIcon', function ($feature) {
            return "<?php if(!auth()->check() || !auth()->user()->hasFeature($feature)): ?>
                <i class='fas fa-lock ml-2 text-yellow-500' title='Disponível no plano Pro'></i>
            <?php endif; ?>";
        });

        Blade::directive('monetizationGate', function ($featureCode) {
            return "<?php 
                \$monetizationResult = app(\App\Services\MonetizationService::class)->checkAccess(auth()->user(), $featureCode);
                if (!\$monetizationResult['allowed']): 
                    if ((\$monetizationResult['action'] ?? '') === 'popup' && !empty(\$monetizationResult['popup'])): ?>
                        <x-upgrade-popup :popup=\"\$monetizationResult['popup']\" />
                    <?php else: ?>
                        <x-plan-lock>
                            <?php echo \$monetizationResult['message'] ?? ''; ?>
                        </x-plan-lock>
                    <?php endif; 
                else: ?>";
        });

        Blade::directive('endMonetizationGate', function () {
            return '<?php endif; ?>';
        });

        // Configuração de Segurança para o Laravel Pulse
        Gate::define('viewPulse', function (User $user) {
            return $user->isAdministrator();
        });

        if (class_exists(Horizon::class)) {
            Horizon::auth(function ($request) {
                $user = $request->user();

                return $user && $user->isAdministrator();
            });
        }
        // Achievements Observers
        WaterEntry::observe(WaterEntryObserver::class);
        ExerciseEntry::observe(ExerciseEntryObserver::class);
        ProfessionalFinanceEntry::observe(ProfessionalFinanceEntryObserver::class);
        HealthAlert::observe(HealthAlertObserver::class);

        if ($this->app->environment('production') && ! (bool) config('session.secure')) {
            Log::warning(
                '[security] SESSION_SECURE_COOKIE=false em produção é risco de hijack de sessão. Defina SESSION_SECURE_COOKIE=true com HTTPS.'
            );
        }
    }
}
