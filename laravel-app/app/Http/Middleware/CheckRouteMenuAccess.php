<?php

namespace App\Http\Middleware;

use App\Services\MenuAccessService;
use App\Services\PanelAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRouteMenuAccess
{
    public function __construct(
        private readonly MenuAccessService $menuAccess
    ) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $routeName = $request->route()?->getName();

        if ($this->menuAccess->shouldBypassMiddleware($routeName)) {
            return $next($request);
        }

        if (! $this->menuAccess->canAccessRoute($user, $routeName, $request)) {
            $panelRedirect = app(PanelAccessService::class)->wrongPanelRedirect($request, $user);
            if ($panelRedirect !== null) {
                return $panelRedirect;
            }

            abort(403, MenuAccessService::DENIED_MESSAGE);
        }

        return $next($request);
    }
}
