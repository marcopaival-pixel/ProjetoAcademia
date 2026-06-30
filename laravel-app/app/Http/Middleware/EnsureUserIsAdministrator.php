<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdministrator
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null || ! $user->hasAdminPanelAccess()) {
            abort(403, 'Acesso reservado a administradores.');
        }

        if (! $user->is_admin && empty($user->academy_company_id)) {
            abort(403, 'Acesso administrativo requer vínculo com organização.');
        }

        $panels = app(\App\Services\PanelAccessService::class);
        if ($panels->currentPanel($user) !== \App\Services\PanelAccessService::PANEL_ADMIN) {
            return redirect($panels->homeRouteForPanel($panels->currentPanel($user)))
                ->with('error', 'Acesso restrito ao painel administrativo.');
        }

        return $next($request);
    }
}
