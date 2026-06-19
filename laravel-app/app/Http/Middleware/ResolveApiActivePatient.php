<?php

namespace App\Http\Middleware;

use App\Support\Api\V1ErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveApiActivePatient
{
    /**
     * Resolve paciente/aluno ativo para rotas profissionais (header X-Active-Patient-Id).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        if ($user->hasRole(['aluno', 'paciente'])) {
            $request->attributes->set('active_patient_id', (int) $user->id);

            return $next($request);
        }

        $header = $request->header('X-Active-Patient-Id');
        if ($header === null || $header === '') {
            $request->attributes->set('active_patient_id', null);

            return $next($request);
        }

        if (! ctype_digit((string) $header)) {
            return V1ErrorResponse::make('Paciente ativo inválido.', 422, 'invalid_active_patient');
        }

        $patientId = (int) $header;

        if ($user->isProfessional() && ! $user->patients()->where('users.id', $patientId)->exists()) {
            return V1ErrorResponse::make('Sem vínculo com este aluno.', 403, 'forbidden');
        }

        $request->attributes->set('active_patient_id', $patientId);

        return $next($request);
    }
}
