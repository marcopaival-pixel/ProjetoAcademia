<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Concerns\GuardsAiCredits;
use App\Http\Controllers\Controller;
use App\Services\AI\OrchestratorService;
use App\Services\AiCreditService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrchestratorController extends Controller
{
    use GuardsAiCredits;

    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * Processa uma solicitação de IA via orquestrador
     */
    public function process(Request $request, AiCreditService $credits): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'clinicId' => 'nullable|integer',
            'context' => 'nullable|array',
        ]);

        $user = $request->user();
        $message = $request->input('message');
        $context = $request->input('context', []);

        if ($request->has('clinicId') && ! isset($context['clinicId'])) {
            $context['clinicId'] = $request->input('clinicId');
        }

        if (isset($context['clinicId']) && $user->academy_company_id != $context['clinicId'] && ! $user->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'error' => 'Acesso negado: Isolamento de clínica violado.',
            ], 403);
        }

        if ($denied = $this->denyIfInsufficientAiCredits($user, 'ai_orchestrator', $credits)) {
            return $denied;
        }

        $result = $this->orchestrator->run($user, $message, $context);

        if (($result['status'] ?? null) === 'success') {
            $referenceId = hash('sha256', implode('|', [
                $user->id,
                $message,
                (string) ($result['intent'] ?? ''),
                now()->format('Y-m-d-H'),
            ]));

            $credits->consume($user, 'ai_orchestrator', [
                'source' => 'orchestrator_api',
                'intent' => $result['intent'] ?? null,
            ], $referenceId);
        }

        return response()->json($result);
    }
}
