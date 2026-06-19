<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAiOrchestratorJob;
use App\Services\AI\OrchestratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OrchestratorController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * Processa uma solicitação de IA via orquestrador.
     */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'clinicId' => 'nullable|integer',
            'context' => 'nullable|array',
            'async' => 'nullable|boolean',
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

        $useAsync = ($request->boolean('async') || $request->boolean('context.async'))
            && config('ai.queue_enabled', false);

        if ($useAsync) {
            $jobKey = 'ai_job_'.$user->id.'_'.Str::uuid()->toString();
            ProcessAiOrchestratorJob::dispatch($user->id, $message, $context, $jobKey);

            return response()->json([
                'status' => 'processing',
                'job_key' => $jobKey,
                'poll_url' => route('api.ai.orchestrator.status', ['jobKey' => $jobKey]),
            ], 202);
        }

        $result = $this->orchestrator->run($user, $message, $context);

        return response()->json($result);
    }

    /**
     * Consulta resultado de job assíncrono de IA.
     */
    public function status(Request $request, string $jobKey): JsonResponse
    {
        $user = $request->user();
        $expectedPrefix = 'ai_job_'.$user->id.'_';

        if (! str_starts_with($jobKey, $expectedPrefix)) {
            return response()->json([
                'status' => 'error',
                'error' => 'Acesso negado: job não pertence ao utilizador autenticado.',
            ], 403);
        }

        $result = Cache::get($jobKey);

        if ($result === null) {
            return response()->json(['status' => 'processing']);
        }

        return response()->json($result);
    }
}
