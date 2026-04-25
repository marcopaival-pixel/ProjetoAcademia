<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileCompletionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->hasAdminPanelAccess()) {
            return $next($request);
        }

        // O onboarding/completitude de perfil só se aplica ao perfil de "aluno".
        // Pacientes em modo somente leitura (Portal do Paciente) não devem ver ou ser bloqueados por isso.
        if ($user && $user->isOnboardingPending() && $user->hasRole('aluno')) {
            // Rotas que exigem perfil completo para funcionar corretamente
            $restrictedRoutes = [
                'nutrition.index',
                'assessments.index',
                'body-analysis.index',
                'report'
            ];

            if ($request->routeIs($restrictedRoutes)) {
                return redirect()->route('dashboard')
                    ->with('toast_error', 'Complete seu perfil para liberar esta funcionalidade!');
            }

            // Garante que o modal seja mostrado no dashboard ou na busca de profissionais
            $onboardingRoutes = ['dashboard', 'patient.professionals.search', 'professional.search'];
            if ($request->routeIs($onboardingRoutes) && !$request->session()->has('show_onboarding_modal')) {
                $request->session()->flash('show_onboarding_modal', true);
            }
        }

        return $next($request);
    }
}
