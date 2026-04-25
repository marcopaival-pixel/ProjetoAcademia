<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationApproved
{
    /**
     * Garante que contas de aluno só acedem à app após aprovação administrativa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        // Administradores têm acesso livre total e bypass antecipado
        if ($user->hasAdminPanelAccess()) {
            return $next($request);
        }

        // Permitir QUALQUER requisição AJAX ou JSON de usuários logados
        // Isso evita SyntaxError no frontend quando o middleware tenta redirecionar uma API
        if ($request->expectsJson() || $request->ajax()) {
            return $next($request);
        }

        if ($user->hasAdminPanelAccess()) {
            return $next($request);
        }

        if ($user->registration_approval_status === 'rejected') {
            $allowed = $request->routeIs(
                'registration.rejected', 
                'logout',
                'onboarding.*'
            );

            if (! $allowed) {
                return redirect()->route('registration.rejected');
            }

            return $next($request);
        }

        if ($user->registration_approval_status === 'pending') {
            $allowed = $request->routeIs(
                'registration.pending',
                'verification.*',
                'email-verification.failed',
                'email-verification.success',
                'logout',
                'theme',
                'onboarding.*',
                'notifications.*',
                'messages.*'
            );

            if (! $allowed) {
                return redirect()->route('registration.pending');
            }

            return $next($request);
        }

        return $next($request);
    }
}
