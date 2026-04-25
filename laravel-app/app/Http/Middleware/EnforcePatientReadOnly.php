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

        // Bloqueia POST, PUT, DELETE apenas para usuários com o papel 'paciente' (convidados por profissionais)
        if ($user && $user->hasRole('paciente') && !$request->isMethod('GET')) {
            
            // Lista de exceções permitidas (operações básicas de conta e onboarding)
            $allowedRoutes = [
                'logout',
                'theme',
                'onboarding/*',
                'patient/complete-profile',
                'profile/select',
                'profile/selection',
            ];

            foreach ($allowedRoutes as $pattern) {
                if ($request->is($pattern) || $request->is('api/' . $pattern)) {
                    return $next($request);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Operação não permitida.',
                    'message' => 'O Portal do Paciente está configurado para modo somente leitura (Read-Only).'
                ], 403);
            }

            return redirect()->back()->with('error', 'O Portal do Paciente é apenas para consulta. Alterações não são permitidas.');
        }

        return $next($request);
    }
}
