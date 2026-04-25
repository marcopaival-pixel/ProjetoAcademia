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
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $bp = (string) config('projeto.base_path');
        if ($bp !== '' && app()->runningInConsole() === false) {
            URL::forceRootUrl(rtrim((string) config('app.url'), '/').$bp);
        }

        // Aplica as configurações de e-mail do banco de dados (fallback global)
        MailConfigService::apply();

        Event::listen(NotificationSending::class, function (NotificationSending $event) {
            if ($event->channel === 'mail' && $event->notifiable instanceof \App\Models\User) {
                MailConfigService::apply($event->notifiable->academy_company_id);
            }
        });

        Event::listen(NotificationSent::class, [MailNotificationAuditListener::class, 'handleSent']);
        Event::listen(NotificationFailed::class, [MailNotificationAuditListener::class, 'handleFailed']);

        View::composer('layouts.app', function ($view) {
            $view->with([
                'projetoTheme' => Theme::current(),
                'themeExplicit' => Theme::isExplicit(),
                'themeNext' => Theme::nextFromRequest(),
            ]);
        });

        View::composer('partials.admin-sidebar', function ($view) {
            $user = auth()->user();
            $map = [];
            if ($user !== null) {
                $map = app(MenuAccessService::class)->getAdminNavVisibilityMap($user);
            }
            $view->with('adminNavVisible', $map);
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
    }
}
