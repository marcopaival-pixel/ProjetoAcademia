<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\OrchestratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrchestratorController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * Processa uma solicitação de IA via orquestrador
     */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'clinicId' => 'nullable|integer',
            'context' => 'nullable|array'
        ]);

        $user = $request->user();
        $message = $request->input('message');
        $context = $request->input('context', []);
        
        // Garantir que o clinicId do request seja passado no contexto se não houver no context array
        if ($request->has('clinicId') && !isset($context['clinicId'])) {
            $context['clinicId'] = $request->input('clinicId');
        }

        // Validação de segurança: isolamento por clinic_id
        if (isset($context['clinicId']) && $user->academy_company_id != $context['clinicId'] && !$user->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'error' => 'Acesso negado: Isolamento de clínica violado.'
            ], 403);
        }

        $result = $this->orchestrator->run($user, $message, $context);

        return response()->json($result);
    }
}
