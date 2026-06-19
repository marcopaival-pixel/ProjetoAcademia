<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePatientReadOnly
{
    /**
     * Garante que pacientes no Portal só possam fazer requisições GET.
     * Bloqueia POST, PUT, DELETE para usuários com perfil 'aluno' (Paciente).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Bloqueia apenas se o usuário for EXCLUSIVAMENTE paciente (ou não tiver papéis de escrita)
        // Se for admin, profissional, recepcionista ou aluno pagante, o bloqueio não se aplica.
        $hasWritingRole = $user->isAdministrator() || $user->hasRole(['professional', 'instructor', 'receptionist', 'aluno', 'manager']);

        if ($user->hasRole('paciente') && !$hasWritingRole && !$request->isMethod('GET')) {
            
            // Permite rotas internas de monitoramento (Pulse, etc)
            if ($request->is('pulse/*')) {
                return $next($request);
            }
            
            // Lista de exceções permitidas (operações básicas de conta e onboarding)
            $allowedRoutes = [
                'logout',
                'admin.logout',
                'theme',
                'patient.activate.process',
                'patient.profile.store',
                'patient.professional.select',
                'patient.dashboard.choice',
                'clinic.selector',
                'clinic.select',
                'messages.*',
            ];

            if ($request->routeIs($allowedRoutes)) {
                return $next($request);
            }

            // Caso especial: Permite alteração de perfil APENAS se for troca de senha
            if ($request->routeIs('profile.update') && $request->input('profile_action') === 'password') {
                return $next($request);
            }

            \Log::warning('ReadOnly Blocked | User: ' . $user->email . ' | Route: ' . ($request->route() ? $request->route()->getName() : 'N/A') . ' | Path: ' . $request->path() . ' | Method: ' . $request->method());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'O Portal do Paciente é apenas para consulta. Alterações não são permitidas.'
                ], 403);
            }

            return redirect()->back()->with('error', 'O Portal do Paciente é apenas para consulta. Alterações não são permitidas.');
        }

        return $next($request);
    }
}
