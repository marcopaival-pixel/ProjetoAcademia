<?php

namespace App\Providers;

use App\Support\Theme;
use App\Services\MailConfigService;
use App\Services\MenuAccessService;
use Illuminate\Cache\RateLimiting\Limit;
use App\Listeners\MailNotificationAuditListener;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Policies\FinancialReportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\Payment\PaymentGatewayManager::class, function ($app) {
            return new \App\Services\Payment\PaymentGatewayManager($app);
        });

        $this->app->bind(\App\Contracts\PaymentGatewayInterface::class, function ($app) {
            return $app->make(\App\Services\Payment\PaymentGatewayManager::class)->driver();
        });

        $this->app->bind(\App\Services\OCR\OCRServiceInterface::class, \App\Services\OCR\GoogleVisionOCRService::class);
    }

    public function boot(): void
    {
        $financialPolicy = new FinancialReportPolicy;
        Gate::define('admin.financial.dashboard', fn (User $user) => $financialPolicy->viewDashboard($user));
        Gate::define('admin.financial.management', fn (User $user) => $financialPolicy->viewManagement($user));
        Gate::define('admin.financial.reports', fn (User $user) => $financialPolicy->viewReports($user));

        $professionalPatientPolicy = new \App\Policies\ProfessionalPatientPolicy;
        Gate::define('professionalPatient.view', fn (User $user, User $patient) => $professionalPatientPolicy->view($user, $patient));
        Gate::define('professionalPatient.update', fn (User $user, User $patient) => $professionalPatientPolicy->update($user, $patient));
        Gate::define('professionalPatient.delete', fn (User $user, User $patient) => $professionalPatientPolicy->delete($user, $patient));

        $bp = (string) config('projeto.base_path');
        if ($bp !== '' && app()->runningInConsole() === false) {
            URL::forceRootUrl(rtrim((string) config('app.url'), '/').$bp);
        }

        // Aplica as configurações de e-mail do banco de dados (fallback global)
        MailConfigService::apply();
        \App\Services\DynamicConfigService::apply();

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Notifications\Events\NotificationSending::class, function ($event) {
            if ($event->channel === 'mail' && $event->notifiable instanceof \App\Models\User) {
                MailConfigService::apply($event->notifiable->academy_company_id);
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Notifications\Events\NotificationSent::class, [MailNotificationAuditListener::class, 'handleSent']);
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Notifications\Events\NotificationFailed::class, [MailNotificationAuditListener::class, 'handleFailed']);

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $activePatient = null;
            if (auth()->check() && session()->has('active_patient_id')) {
                // Compartilhar apenas os dados básicos para não sobrecarregar
                $activePatient = \App\Models\User::find(session('active_patient_id'));
                if (!$activePatient) {
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

        \Illuminate\Support\Facades\View::composer('partials.topbar', function ($view) {
            $user = auth()->user();
            if ($user) {
                $view->with([
                    'aiUsageToday' => $user->getAiCreditsUsedToday(),
                    'aiUsageMonth' => $user->getAiCreditsUsedThisMonth(),
                    'aiUsageTotal' => $user->getAiCreditsUsedTotal(),
                ]);
            }
        });

        \Illuminate\Support\Facades\View::composer('partials.admin-sidebar', function ($view) {
            $user = auth()->user();
            $map = [];
            if ($user !== null) {
                $map = app(MenuAccessService::class)->getAdminNavVisibilityMap($user);
            }
            $view->with('adminNavVisible', $map);
        });

        \Illuminate\Support\Facades\View::composer('professional.*', function ($view) {
            if (auth()->check() && auth()->user()->hasRole('professional')) {
                $profile = auth()->user()->professionalProfile;
                $professionName = $profile && $profile->profession ? $profile->profession->name : 'Geral';
                
                $isFitness = in_array($professionName, ['Educador Físico', 'Personal Trainer']);
                
                $view->with('patientLabel', $isFitness ? 'Aluno' : 'Paciente');
                $view->with('patientsLabel', $isFitness ? 'Alunos' : 'Pacientes');
            }
        });

        \Illuminate\Support\Facades\RateLimiter::for('openfoodfacts', function (\Illuminate\Http\Request $request) {
            $uid = (int) ($request->user()?->id ?? 0);
            $per = max(5, (int) config('services.openfoodfacts.max_requests_per_minute', 30));

            return \Illuminate\Cache\RateLimiting\Limit::perMinute($per)->by($uid > 0 ? 'off-'.$uid : 'off-ip-'.$request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('privacy-download', function (\Illuminate\Http\Request $request) {
            $uid = (int) ($request->user()?->id ?? 0);

            return \Illuminate\Cache\RateLimiting\Limit::perHour(20)->by($uid > 0 ? 'privacy-u-'.$uid : 'privacy-ip-'.$request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('marketing-tracking', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by('mkt-ip-'.$request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('client-errors', function (\Illuminate\Http\Request $request) {
            $limit = max(1, (int) config('observability.client_errors.rate_limit', 10));

            return \Illuminate\Cache\RateLimiting\Limit::perMinute($limit)->by('client-err-ip-'.$request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            $userId = (int) ($request->user()?->id ?? 0);

            return Limit::perMinute(120)->by($userId > 0 ? 'api-u-'.$userId : 'api-ip-'.$request->ip());
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Queue\Events\JobProcessed::class, function ($event) {
            $durationMs = 0;
            if (isset($event->job) && method_exists($event->job, 'payload')) {
                $payload = $event->job->payload();
                $pushedAt = $payload['pushedAt'] ?? null;
                if ($pushedAt) {
                    $durationMs = (int) max(0, (microtime(true) - (float) $pushedAt) * 1000);
                }
            }

            \App\Services\Operations\JobMetricsRecorder::recordCompleted($durationMs);
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Queue\Events\JobFailed::class, function () {
            \App\Services\Operations\JobMetricsRecorder::recordFailed();
        });

        if (class_exists(\Sentry\SentrySdk::class) && config('sentry.dsn')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
                $scope->setTag('app', 'nexshape');
            });
        }

        // Feature and Plan Directives
        \Illuminate\Support\Facades\Blade::if('feature', function ($key) {
            return auth()->check() && auth()->user()->hasFeature($key);
        });

        \Illuminate\Support\Facades\Blade::if('planLimit', function ($key, $currentCount) {
            if (!auth()->check()) return false;
            $limit = auth()->user()->getPlanLimit($key);
            return $limit === 0 || $currentCount < $limit;
        });

        \Illuminate\Support\Facades\Blade::directive('lockIcon', function ($feature) {
            return "<?php if(!auth()->check() || !auth()->user()->hasFeature($feature)): ?>
                <i class='fas fa-lock ml-2 text-yellow-500' title='Disponível no plano Pro'></i>
            <?php endif; ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('monetizationGate', function ($featureCode) {
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

        \Illuminate\Support\Facades\Blade::directive('endMonetizationGate', function () {
            return "<?php endif; ?>";
        });

        // Configuração de Segurança para o Laravel Pulse
        \Illuminate\Support\Facades\Gate::define('viewPulse', function (\App\Models\User $user) {
            return $user->isAdministrator();
        });

        if (class_exists(\Laravel\Horizon\Horizon::class)) {
            \Laravel\Horizon\Horizon::auth(function ($request) {
                $user = $request->user();

                return $user && $user->isAdministrator();
            });
        }
        // Achievements Observers
        \App\Models\WaterEntry::observe(\App\Observers\WaterEntryObserver::class);
        \App\Models\ExerciseEntry::observe(\App\Observers\ExerciseEntryObserver::class);
        \App\Models\ProfessionalFinanceEntry::observe(\App\Observers\ProfessionalFinanceEntryObserver::class);
        \App\Models\HealthAlert::observe(\App\Observers\HealthAlertObserver::class);

        if ($this->app->environment('production') && ! (bool) config('session.secure')) {
            \Illuminate\Support\Facades\Log::warning(
                '[security] SESSION_SECURE_COOKIE=false em produção — risco de hijack de sessão. Defina SESSION_SECURE_COOKIE=true com HTTPS.'
            );
        }
    }
}
