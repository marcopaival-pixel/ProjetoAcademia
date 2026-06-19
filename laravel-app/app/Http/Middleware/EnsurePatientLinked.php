<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EnsurePatientLinked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Se não houver usuário logado, permite que outros middlewares tratem (auth)
        if (!$user) {
            return $next($request);
        }

        // Exceções: Administradores, Gerentes e Supervisores têm acesso total
        $exemptRoles = ['admin', 'manager', 'supervisor'];
        if ($user->is_admin || $user->hasRole($exemptRoles)) {
            return $next($request);
        }

        // Se for um profissional, verifica o vínculo
        if ($user->hasRole('professional')) {
            // Tenta identificar o paciente nos parâmetros da rota
            $patientSlug = $request->route('patient') ?: $request->route('user') ?: $request->route('id');
            
            if ($patientSlug) {
                $patient = null;

                if ($patientSlug instanceof User) {
                    $patient = $patientSlug;
                } elseif (is_numeric($patientSlug)) {
                    $patient = User::withoutGlobalScopes()->find($patientSlug);
                }

                // Se encontramos um usuário e ele tem papel de aluno/paciente
                if ($patient && ($patient->hasRole('aluno') || $patient->profile_id === null)) {
                    // Verifica se o profissional é o mesmo que o paciente (acesso ao próprio perfil)
                    if ($patient->id === $user->id) {
                        return $next($request);
                    }

                    // Verifica o vínculo na tabela associativa
                    $isLinked = $user->patients()->wherePivot('user_id', $patient->id)->exists();
                    
                    if (!$isLinked) {
                        return abort(403, 'Acesso negado. Este paciente não está vinculado ao seu perfil.');
                    }
                }
            }
        }

        return $next($request);
    }
}
