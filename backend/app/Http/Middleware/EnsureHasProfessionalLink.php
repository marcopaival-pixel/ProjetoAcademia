<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureHasProfessionalLink
{
    /**
     * Garante que o paciente esteja vinculado a pelo menos um profissional ativo.
     */
    public function handle(Request $request, Closure $next)
    {
        // O bloqueio de vínculo profissional foi desativado.
        // O utilizador/aluno agora tem acesso irrestrito ao sistema.
        return $next($request);
    }
}
