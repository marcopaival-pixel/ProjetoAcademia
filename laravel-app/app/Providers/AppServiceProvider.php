<?php

namespace App\Providers;

use App\Support\Theme;
use Illuminate\Cache\RateLimiting\Limit;
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

        View::composer('layouts.app', function ($view) {
            $view->with([
                'projetoTheme' => Theme::current(),
                'themeExplicit' => Theme::isExplicit(),
                'themeNext' => Theme::nextFromRequest(),
            ]);
        });

        RateLimiter::for('openfoodfacts', function (Request $request) {
            $uid = (int) ($request->user()?->id ?? 0);
            $per = max(5, (int) config('services.openfoodfacts.max_requests_per_minute', 30));

            return Limit::perMinute($per)->by($uid > 0 ? 'off-'.$uid : 'off-ip-'.$request->ip());
        });
    }
}
