<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\GuardsAiCredits;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WorkoutPhotoImportController;
use App\Models\WorkoutImportLog;
use App\Services\AI\WorkoutImportOrchestrator;
use App\Services\AiCreditService;
use App\Services\MonetizationService;
use App\Services\WorkoutImportBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutImportController extends Controller
{
    use GuardsAiCredits;

    public function validatePhoto(
        Request $request,
        WorkoutPhotoImportController $controller,
        MonetizationService $monetization,
        AiCreditService $credits,
    ): JsonResponse {
        if ($denied = $this->denyIfWorkoutImportBlocked($request->user(), $monetization, $credits)) {
            return $denied;
        }

        $response = $controller->validatePhoto($request);
        $payload = $response->getData(true);

        if (($payload['success'] ?? false) !== true) {
            return response()->json([
                'error' => [
                    'message' => $payload['error'] ?? 'Falha ao validar imagem.',
                ],
            ], $response->getStatusCode());
        }

        unset($payload['success']);

        return response()->json(['data' => $payload], $response->getStatusCode());
    }

    public function process(
        Request $request,
        WorkoutPhotoImportController $controller,
        WorkoutImportOrchestrator $orchestrator,
        MonetizationService $monetization,
        AiCreditService $credits,
        WorkoutImportBillingService $billing,
    ): JsonResponse {
        if ($denied = $this->denyIfWorkoutImportBlocked($request->user(), $monetization, $credits)) {
            return $denied;
        }

        $request->validate([
            'photo' => 'required|image|max:10240',
        ]);

        try {
            $log = $orchestrator->initializeSession($request->user(), [$request->file('photo')]);
            $orchestrator->runValidation($log);
            $log->refresh();

            if ($log->status !== 'EXTRACTING') {
                return response()->json([
                    'error' => [
                        'message' => $log->error_message ?: 'A imagem nao foi validada como ficha de treino.',
                    ],
                    'data' => [
                        'status' => $log->status,
                        'session' => $log->structured_json,
                    ],
                ], 422);
            }

            $orchestrator->runExtractionAndConsolidation($log);
            $log->refresh();

            if ($log->status !== 'WAITING_REVIEW') {
                return response()->json([
                    'error' => [
                        'message' => $log->error_message ?: 'A ficha foi reconhecida, mas nao foi possivel extrair exercicios para revisao.',
                    ],
                    'data' => [
                        'status' => $log->status,
                        'session' => $log->structured_json,
                    ],
                ], 422);
            }

            $billing->chargeOnSuccessfulExtraction($request->user(), $log, 'api_v1_process');

            return response()->json([
                'data' => [
                    'exercises' => $this->flattenExercises($log),
                    'log_id' => $log->id,
                    'status' => $log->status,
                    'audit_results' => $log->structured_json['audit_results'] ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage() ?: 'Falha ao processar imagem.',
                ],
            ], 422);
        }
    }

    public function save(
        Request $request,
        WorkoutPhotoImportController $controller,
        MonetizationService $monetization,
        AiCreditService $credits,
    ): JsonResponse {
        if ($denied = $this->denyIfWorkoutImportBlocked($request->user(), $monetization, $credits)) {
            return $denied;
        }

        $response = $controller->save($request);
        $payload = $response->getData(true);

        if (($payload['success'] ?? false) !== true) {
            return response()->json([
                'error' => [
                    'message' => $payload['error'] ?? 'Falha ao salvar treino.',
                ],
            ], $response->getStatusCode());
        }

        return response()->json([
            'data' => [
                'message' => $payload['message'] ?? 'Treino importado com sucesso.',
                'plan_id' => $this->extractPlanId($payload['redirect'] ?? null),
            ],
        ], $response->getStatusCode());
    }

    public function orchInitialize(
        Request $request,
        WorkoutImportOrchestrator $orchestrator,
        MonetizationService $monetization,
        AiCreditService $credits,
    ): JsonResponse {
        if ($denied = $this->denyIfWorkoutImportBlocked($request->user(), $monetization, $credits)) {
            return $denied;
        }

        $request->validate([
            'photos' => 'required|array|min:1|max:7',
            'photos.*' => 'required|image|max:10240',
        ]);

        $log = $orchestrator->initializeSession($request->user(), $request->file('photos'));

        return response()->json(['data' => $this->sessionPayload($log)]);
    }

    public function orchValidate(
        Request $request,
        string $uuid,
        WorkoutImportOrchestrator $orchestrator,
        MonetizationService $monetization,
        AiCreditService $credits,
    ): JsonResponse {
        if ($denied = $this->denyIfWorkoutImportBlocked($request->user(), $monetization, $credits)) {
            return $denied;
        }

        $log = WorkoutImportLog::where('image_path', $uuid)->where('user_id', $request->user()->id)->firstOrFail();
        $orchestrator->runValidation($log);
        $log->refresh();

        return response()->json(['data' => $this->sessionPayload($log)]);
    }

    public function orchSubstitute(
        Request $request,
        string $uuid,
        WorkoutImportOrchestrator $orchestrator,
        MonetizationService $monetization,
        AiCreditService $credits,
    ): JsonResponse {
        if ($denied = $this->denyIfWorkoutImportBlocked($request->user(), $monetization, $credits)) {
            return $denied;
        }

        $request->validate([
            'image_id' => 'required|integer',
            'photo' => 'required|image|max:10240',
        ]);

        $log = WorkoutImportLog::where('image_path', $uuid)->where('user_id', $request->user()->id)->firstOrFail();
        $orchestrator->substituteImage($log, (int) $request->input('image_id'), $request->file('photo'));
        $log->refresh();

        return response()->json(['data' => $this->sessionPayload($log)]);
    }

    public function orchProcess(
        Request $request,
        string $uuid,
        WorkoutImportOrchestrator $orchestrator,
        MonetizationService $monetization,
        AiCreditService $credits,
        WorkoutImportBillingService $billing,
    ): JsonResponse {
        if ($denied = $this->denyIfWorkoutImportBlocked($request->user(), $monetization, $credits)) {
            return $denied;
        }

        $log = WorkoutImportLog::where('image_path', $uuid)->where('user_id', $request->user()->id)->firstOrFail();
        try {
            $orchestrator->runExtractionAndConsolidation($log);
            $log->refresh();

            if ($log->status === 'WAITING_REVIEW') {
                $billing->chargeOnSuccessfulExtraction($request->user(), $log, 'api_v1_orch_process');
            }
        } catch (\Throwable $e) {
            $message = $this->safeImportErrorMessage($e->getMessage());

            return response()->json([
                'data' => [
                    'uuid' => $log->image_path,
                    'status' => $this->isAiServiceError($e->getMessage()) ? 'AI_SERVICE_ERROR' : 'EXTRACTION_FAILED',
                    'error_message' => $message,
                    'exercises' => [],
                ],
            ], 422);
        }

        $payload = $this->sessionPayload($log);
        $payload['exercises'] = $this->flattenExercises($log);

        return response()->json(['data' => $payload], $log->status === 'WAITING_REVIEW' ? 200 : 422);
    }

    public function orchStatus(Request $request, string $uuid): JsonResponse
    {
        $log = WorkoutImportLog::where('image_path', $uuid)->where('user_id', $request->user()->id)->firstOrFail();

        return response()->json(['data' => $this->sessionPayload($log)]);
    }

    private function extractPlanId(?string $redirect): ?int
    {
        if (!$redirect) {
            return null;
        }

        preg_match('/(\d+)(?:\D*)$/', $redirect, $matches);

        return isset($matches[1]) ? (int) $matches[1] : null;
    }

    private function sessionPayload(WorkoutImportLog $log): array
    {
        return [
            'uuid' => $log->image_path,
            'status' => $log->status,
            'session' => $log->structured_json,
            'error_message' => $log->error_message,
            'log_id' => $log->id,
        ];
    }

    private function safeImportErrorMessage(string $message): string
    {
        return $this->isAiServiceError($message)
            ? 'O servico de inteligencia artificial esta temporariamente indisponivel. Verifique a configuracao e tente novamente.'
            : ($message ?: 'Falha ao processar as imagens de treino.');
    }

    private function isAiServiceError(string $message): bool
    {
        $lowered = strtolower($message);

        return str_contains($lowered, 'openai')
            || str_contains($lowered, 'api key')
            || str_contains($lowered, 'api_key')
            || str_contains($lowered, 'quota')
            || str_contains($lowered, 'billing')
            || str_contains($lowered, 'token');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function flattenExercises(WorkoutImportLog $log): array
    {
        $payload = $log->structured_json['consolidated_workout'] ?? [];
        $consolidated = $payload['consolidated_workout'] ?? $payload['days'] ?? $payload;
        $flat = [];

        if (is_array($consolidated)) {
            foreach ($consolidated as $day => $dayPayload) {
                $exercises = $dayPayload['exercises'] ?? $dayPayload['exercicios'] ?? (is_array($dayPayload) ? $dayPayload : []);

                if (! is_array($exercises)) {
                    continue;
                }

                foreach ($exercises as $exercise) {
                    if (! is_array($exercise)) {
                        continue;
                    }

                    $flat[] = [
                        'nome_exercicio' => $exercise['nome_exercicio'] ?? $exercise['name'] ?? $exercise['exercise_name'] ?? 'Exercicio importado',
                        'series' => $exercise['series'] ?? $exercise['sets'] ?? null,
                        'repeticoes' => $exercise['repeticoes'] ?? $exercise['reps'] ?? null,
                        'carga' => $exercise['carga'] ?? $exercise['load'] ?? null,
                        'intervalo' => $exercise['intervalo'] ?? $exercise['rest'] ?? null,
                        'observacoes' => $exercise['observacoes'] ?? $exercise['notes'] ?? null,
                        'day' => is_string($day) ? $day : ($exercise['day'] ?? null),
                        'confidence_scores' => $exercise['confidence_scores'] ?? [],
                    ];
                }
            }
        }

        if ($flat === []) {
            foreach (($log->structured_json['extracted_workouts'] ?? []) as $extracted) {
                foreach (($extracted['exercises'] ?? $extracted['exercicios'] ?? []) as $exercise) {
                    if (! is_array($exercise)) {
                        continue;
                    }

                    $flat[] = [
                        'nome_exercicio' => $exercise['nome_exercicio'] ?? $exercise['name'] ?? 'Exercicio importado',
                        'series' => $exercise['series'] ?? null,
                        'repeticoes' => $exercise['repeticoes'] ?? null,
                        'carga' => $exercise['carga'] ?? null,
                        'intervalo' => $exercise['intervalo'] ?? null,
                        'observacoes' => $exercise['observacoes'] ?? null,
                        'day' => $exercise['day'] ?? null,
                        'confidence_scores' => $exercise['confidence_scores'] ?? [],
                    ];
                }
            }
        }

        return $flat;
    }
}
