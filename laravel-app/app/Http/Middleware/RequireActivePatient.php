<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActivePatient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Se for profissional e não tiver paciente ativo na sessão
        if ($user && $user->isProfessional() && !session()->has('active_patient_id')) {
            
            // Tenta restaurar o último paciente acessado
            $lastAccessedPatient = \App\Models\ProfessionalPatient::where('profissional_id', $user->id)
                ->whereNotNull('last_accessed_at')
                ->whereIn('status', ['Sim', 'PENDENTE'])
                ->orderBy('last_accessed_at', 'desc')
                ->first();
                
            if ($lastAccessedPatient) {
                session(['active_patient_id' => $lastAccessedPatient->user_id]);
                return $next($request);
            }

            // Permitir acesso ao Dashboard e rotas do próprio profissional
            $allowedRoutes = [
                'professional.dashboard',
                'professional.patients.index',
                'professional.patients.search',
                'professional.active-patient.set',
                'professional.active-patient.clear',
                'professional.profile.edit',
                'professional.profile.update',
                'dashboard' // Dashboard genérico, que redireciona conforme o perfil
            ];

            if (!$request->routeIs($allowedRoutes) && !$request->is('professional/patients/*') && !$request->is('logout') && !$request->is('api/*')) {
                return redirect()->route('professional.dashboard')
                    ->with('error', 'Selecione um aluno antes de continuar.');
            }
        }

        return $next($request);
    }
}
