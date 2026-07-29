<?php

namespace App\Http\Middleware;

use App\Models\ProfessionalPatient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActivePatientContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $contextId = $request->header('X-Active-Context')
            ?? $request->header('X-Active-Context-ID')
            ?? $request->query('active_context');

        if (! $contextId) {
            return response()->json([
                'error' => 'Contexto de vínculo não informado.',
                'code' => 'MISSING_CONTEXT',
            ], 400);
        }

        // Android pode enviar "type:id" para contextos de aluno; paciente usa ID numérico do vínculo.
        if (is_string($contextId) && str_contains($contextId, ':')) {
            [, $contextId] = explode(':', $contextId, 2);
        }

        $contextId = (int) $contextId;
        if ($contextId <= 0) {
            return response()->json([
                'error' => 'Contexto de vínculo inválido.',
                'code' => 'INVALID_CONTEXT',
            ], 400);
        }

        $userId = Auth::id();

        $link = ProfessionalPatient::where('id', $contextId)
            ->where('user_id', $userId)
            ->where('status', 'Sim')
            ->first();

        if (!$link) {
            return response()->json([
                'error' => 'Vínculo não encontrado, inativo ou você não tem acesso.',
                'code' => 'INVALID_CONTEXT'
            ], 403);
        }

        // Injeta o vínculo validado no request para ser usado pelos controllers
        $request->attributes->set('active_patient_link', $link);

        return $next($request);
    }
}
