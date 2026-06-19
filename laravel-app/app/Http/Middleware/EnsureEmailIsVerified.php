<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Skip verification for administradores, staff com admin.access e representantes
            if ($user->hasAdminPanelAccess() || $user->hasRole('representative')) {
                return $next($request);
            }

            $verificacaoAtiva = \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true);

            if ($verificacaoAtiva && !$user->isEmailVerified()) {
                // Apenas fluxo de verificação e saída. /register não é exceção: utilizador autenticado sem
                // confirmação só vê /verify-email (cadastro só fica "ativo" após confirmar o e-mail).
                $allowed = $request->routeIs('verification.*')
                    || $request->routeIs('email-verification.failed')
                    || $request->routeIs('registration.pending')
                    || $request->routeIs('registration.rejected')
                    || $request->routeIs('logout');

                if (! $allowed) {
                    return $request->expectsJson()
                        ? abort(403, 'Seu endereço de e-mail não foi verificado.')
                        : redirect()->route('verification.notice', ['email' => $user->email]);
                }
            }
        }

        return $next($request);
    }
}
