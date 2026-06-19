<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\PanelAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePanelIsolation
{
    public function __construct(
        private PanelAccessService $panels,
    ) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $this->panels->shouldEnforce($request, $user)) {
            return $next($request);
        }

        if ($redirect = $this->panels->wrongPanelRedirect($request, $user)) {
            if ($request->expectsJson()) {
                abort(403, 'Acesso restrito ao painel do seu perfil atual.');
            }

            return $redirect;
        }

        $pathPanel = $this->panels->detectPanelFromPath('/'.$request->path());
        if ($pathPanel !== null
            && $pathPanel === PanelAccessService::PANEL_ADMIN
            && ! $user->hasAdminPanelAccess()) {
            abort(403, 'Acesso reservado a administradores.');
        }

        return $next($request);
    }
}
