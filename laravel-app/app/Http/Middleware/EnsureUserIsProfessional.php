<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsProfessional
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isProfessional()) {
            abort(403, 'Acesso reservado a profissionais.');
        }

        $panels = app(\App\Services\PanelAccessService::class);
        if ($panels->currentPanel($user) !== \App\Services\PanelAccessService::PANEL_PROFESSIONAL) {
            return redirect($panels->homeRouteForPanel($panels->currentPanel($user)))
                ->with('error', 'Acesso restrito ao painel profissional.');
        }

        return $next($request);
    }
}
