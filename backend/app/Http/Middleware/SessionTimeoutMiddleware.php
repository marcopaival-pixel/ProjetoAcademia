<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SessionTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->last_activity_at) {
            $timeoutMinutes = $this->getTimeoutMinutes($user);
            $lastActivity = \Carbon\Carbon::parse($user->last_activity_at);
            
            if ($lastActivity->diffInMinutes(now()) >= $timeoutMinutes) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['message' => 'Sua sessão expirou por segurança. Entre novamente para continuar.', 'expired' => true], 401);
                }
                
                return redirect()->route('login')->with('error', 'Sua sessão expirou por segurança. Entre novamente para continuar.');
            }
        }

        if ($user) {
            // Apenas atualiza a última atividade se for uma requisição marcada com atividade de usuário explícita (pelo frontend)
            // ou se não houver um header X-User-Activity sendo verificado rigidamente (para retrocompatibilidade com requisições web puras).
            // Requisições normais web (sem AJAX) vão considerar como atividade. AJAX sem X-User-Activity vai ser ignorado como atividade.
            
            $isAjax = $request->ajax() || $request->wantsJson() || $request->is('api/*');
            $hasActivityHeader = $request->hasHeader('X-User-Activity') && $request->header('X-User-Activity') === 'true';

            if (! $isAjax || $hasActivityHeader) {
                // Atualiza o last_activity_at silenciosamente
                $user->last_activity_at = now();
                $user->saveQuietly();
            }
        }

        return $next($request);
    }

    private function getTimeoutMinutes($user): int
    {
        if ($user->isSuperAdmin()) {
            return 15;
        }

        if ($user->hasRole(['paciente', 'aluno'])) {
            return 60;
        }

        // Profissional, Clínica, Academia, Recepcionista, Administrador e outros caem no padrão de 30 minutos
        return 30;
    }
}
