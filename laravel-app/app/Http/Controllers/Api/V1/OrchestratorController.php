<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAiOrchestratorJob;
use App\Services\AI\OrchestratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OrchestratorController extends Controller
{
    use FormatsApiResponses;

    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    public function process(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'context' => ['nullable', 'array'],
            'async' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $context = array_merge($validated['context'] ?? [], [
            'source' => 'api_v1',
            'clinicId' => $user->academy_company_id,
            'clinic_id' => $user->clinic_id,
        ]);

        if (isset($context['clinicId']) && $user->academy_company_id != $context['clinicId'] && ! $user->isAdministrator()) {
            return $this->error('Acesso negado: isolamento de clínica violado.', 403, 'tenant_violation');
        }

        $useAsync = ($request->boolean('async') || ($context['async'] ?? false))
            && config('ai.queue_enabled', false);

        if ($useAsync) {
            $jobKey = 'ai_job_'.$user->id.'_'.Str::uuid()->toString();
            ProcessAiOrchestratorJob::dispatch($user->id, $validated['message'], $context, $jobKey);

            return $this->success([
                'status' => 'processing',
                'job_key' => $jobKey,
            ], status: 202);
        }

        $result = $this->orchestrator->run($user, $validated['message'], $context);

        return response()->json(['data' => $result]);
    }

    public function status(Request $request, string $jobKey): JsonResponse
    {
        $user = $request->user();
        $expectedPrefix = 'ai_job_'.$user->id.'_';

        if (! str_starts_with($jobKey, $expectedPrefix)) {
            return $this->error('Job não pertence ao utilizador autenticado.', 403, 'forbidden');
        }

        $result = Cache::get($jobKey);

        if ($result === null) {
            return $this->success(['status' => 'processing']);
        }

        return response()->json(['data' => $result]);
    }
}
