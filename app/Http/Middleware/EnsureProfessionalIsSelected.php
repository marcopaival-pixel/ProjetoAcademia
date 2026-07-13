<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfessionalIsSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->hasRole('paciente')) {
            if (session('active_role') && session('active_role') !== 'paciente') {
                return redirect()->route('dashboard')
                    ->with('error', 'Altere para o perfil Paciente para acessar este painel.');
            }

            if (Session::has('active_professional_id')) {
                $hasActiveLink = $user->professionals()
                    ->where('profissional_id', Session::get('active_professional_id'))
                    ->wherePivot('status', 'Sim')
                    ->exists();

                if ($hasActiveLink) {
                    return $next($request);
                }

                Session::forget('active_professional_id');
            }

            $allowedRoutes = [
                'patient.professional.selection',
                'patient.professional.select',
                'patient.dashboard.choice',
                'patient.unified.dashboard',
                'patient.activate.show',
                'patient.activate.process',
                'patient.profile.complete',
                'patient.profile.store',
                'logout',
            ];

            if ($request->routeIs($allowedRoutes)) {
                return $next($request);
            }

            $professionals = $user->professionals()->wherePivot('status', 'Sim')->get();
            if ($professionals->count() === 1) {
                Session::put('active_professional_id', $professionals->first()->id);

                return $next($request);
            }

            if ($professionals->count() > 1) {
                return redirect()->route('patient.professional.selection');
            }
        }

        return $next($request);
    }
}
