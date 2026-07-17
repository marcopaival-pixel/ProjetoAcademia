<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\WorkoutImportLog;
use App\Services\AI\Agents\WorkoutValidatorAgent;
use App\Services\AI\Agents\WorkoutExtractorAgent;
use App\Services\AI\Agents\WorkoutConsolidatorAgent;
use App\Services\AI\Agents\WorkoutAuditorAgent;
use App\Services\SecureFileService;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WorkoutImportOrchestrator
{
    public function __construct(
        private WorkoutValidatorAgent $validator,
        private WorkoutExtractorAgent $extractor,
        private WorkoutConsolidatorAgent $consolidator,
        private WorkoutAuditorAgent $auditor,
        private SecureFileService $secureFiles,
        private WorkoutScanLearningService $scanLearning
    ) {}

    /**
     * Inicializa uma nova sessão de importação.
     */
    public function initializeSession(User $user, array $uploadedFiles): WorkoutImportLog
    {
        $uuid = (string) Str::uuid();
        
        $images = [];
        foreach ($uploadedFiles as $index => $file) {
            $path = $this->secureFiles->storeSensitiveFile($file, 'workout_imports');
            $images[] = [
                'image_id' => $index + 1,
                'image_path' => $path,
                'day' => null,
                'is_workout' => null,
                'confidence' => null,
                'reason' => null,
                'detected_elements' => []
            ];
        }

        $sessionData = [
            'uuid' => $uuid,
            'images' => $images,
            'extracted_workouts' => [],
            'consolidated_workout' => null,
            'audit_results' => null,
            'progress' => [
                'current_image' => 0,
                'total_images' => count($images),
                'percent' => 0
            ]
        ];

        return WorkoutImportLog::create([
            'user_id' => $user->id,
            'status' => 'RECEIVED',
            'image_path' => $uuid, // Usamos o UUID como chave de busca rápida no campo image_path
            'structured_json' => $sessionData
        ]);
    }

    /**
     * Executa a validação de imagens (Etapa 1).
     */
    public function runValidation(WorkoutImportLog $log): void
    {
        $log->update(['status' => 'VALIDATING']);
        $data = $log->structured_json;
        $user = $log->user;

        $overallValid = true;
        foreach ($data['images'] as $idx => &$img) {
            // Se já foi validada e é válida, não reprocessa
            if ($img['is_workout'] === true) {
                continue;
            }

            $absolutePath = storage_path('app/private/' . $img['image_path']);
            if (!file_exists($absolutePath)) {
                $absolutePath = storage_path('app/public/' . $img['image_path']);
            }

            try {
                $result = $this->validator->execute($user, 'Valide se esta imagem é uma ficha de treino.', [
                    'image_path' => $absolutePath,
                    'clinic_id' => $user->clinic_id,
                    'clinicId' => $user->academy_company_id,
                ]);

                if (!$result['ok']) {
                    throw new Exception($result['error'] ?? 'Erro desconhecido na validação.');
                }

                $valData = $result['validation_data'] ?? [];
                $img['is_workout'] = (bool)($valData['is_workout'] ?? false);
                $img['confidence'] = (float)($valData['confidence'] ?? 0.0);
                $img['document_type'] = (string)($valData['document_type'] ?? 'unknown');
                $img['day'] = $valData['detected_day'] ?? null;
                $img['reason'] = (string)($valData['reason'] ?? '');
                $img['detected_elements'] = $valData['detected_elements'] ?? [];

                if (!$img['is_workout']) {
                    $overallValid = false;
                    $this->scanLearning->recordFailure(
                        log: $log,
                        failedStage: 'validation',
                        errorType: 'invalid_workout_image',
                        imagePath: $img['image_path'] ?? null,
                        aiIdentifiedContent: $valData,
                        expectedResult: ['is_workout' => true],
                        errorMessage: $img['reason'] ?? null
                    );
                }

            } catch (Exception $e) {
                Log::error("Erro na validação da imagem {$img['image_id']}: " . $e->getMessage());

                if ($this->isAiServiceError($e->getMessage())) {
                    $log->update([
                        'status' => 'AI_SERVICE_ERROR',
                        'error_message' => $this->safeAiErrorMessage($e->getMessage()),
                        'structured_json' => $data,
                    ]);
                    $this->scanLearning->recordFailure(
                        log: $log,
                        failedStage: 'validation',
                        errorType: 'ai_service_error',
                        imagePath: $img['image_path'] ?? null,
                        aiIdentifiedContent: $img,
                        expectedResult: ['status' => 'VALIDATED'],
                        errorMessage: $this->safeAiErrorMessage($e->getMessage())
                    );
                    return;
                }

                $img['is_workout'] = false;
                $img['reason'] = 'Erro ao processar imagem: ' . $this->safeAiErrorMessage($e->getMessage());
                $overallValid = false;
                $this->scanLearning->recordFailure(
                    log: $log,
                    failedStage: 'validation',
                    errorType: 'validation_exception',
                    imagePath: $img['image_path'] ?? null,
                    aiIdentifiedContent: $img,
                    expectedResult: ['is_workout' => true],
                    errorMessage: $this->safeAiErrorMessage($e->getMessage())
                );
            }

            $data['progress']['current_image'] = $idx + 1;
            $data['progress']['percent'] = Math_round((($idx + 1) / count($data['images'])) * 100);
            $log->update(['structured_json' => $data]);
        }

        if (!$overallValid) {
            $log->update(['status' => 'INVALID_IMAGE', 'structured_json' => $data]);
            return;
        }

        $log->update(['status' => 'EXTRACTING', 'structured_json' => $data]);
    }

    /**
     * Substitui uma imagem inválida na sessão e redefine seu status de validação.
     */
    public function substituteImage(WorkoutImportLog $log, int $imageId, $newFile): void
    {
        $data = $log->structured_json;
        
        foreach ($data['images'] as &$img) {
            if ((int)$img['image_id'] === $imageId) {
                // Remove imagem antiga se existir
                $oldPath = storage_path('app/private/' . $img['image_path']);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }

                // Salva a nova imagem
                $path = $this->secureFiles->storeSensitiveFile($newFile, 'workout_imports');
                $img['image_path'] = $path;
                $img['is_workout'] = null;
                $img['confidence'] = null;
                $img['day'] = null;
                $img['reason'] = null;
                $img['detected_elements'] = [];
                break;
            }
        }

        $log->update([
            'status' => 'RECEIVED',
            'structured_json' => $data
        ]);
    }

    /**
     * Executa a extração, consolidação e auditoria.
     */
    public function runExtractionAndConsolidation(WorkoutImportLog $log): void
    {
        $data = $log->structured_json;
        $user = $log->user;

        // --- 1. EXTRAÇÃO ---
        $log->update(['status' => 'EXTRACTING']);
        $extractedWorkouts = $data['extracted_workouts'] ?? [];

        foreach ($data['images'] as $img) {
            $imgId = $img['image_id'];
            
            // Se já foi extraído anteriormente, evita reprocessar
            if (isset($extractedWorkouts[$imgId])) {
                continue;
            }

            $absolutePath = storage_path('app/private/' . $img['image_path']);
            if (!file_exists($absolutePath)) {
                $absolutePath = storage_path('app/public/' . $img['image_path']);
            }

            $result = $this->extractor->execute($user, 'Extraia os dados estruturados de treino contidos na imagem.', [
                'image_path' => $absolutePath,
                'clinic_id' => $user->clinic_id,
                'clinicId' => $user->academy_company_id,
            ]);

            if (!$result['ok']) {
                $this->scanLearning->recordFailure(
                    log: $log,
                    failedStage: 'extraction',
                    errorType: 'extractor_failed',
                    imagePath: $img['image_path'] ?? null,
                    aiIdentifiedContent: $result,
                    expectedResult: ['extracted_data' => 'structured_workout_json'],
                    errorMessage: $result['error'] ?? 'Falha na extracao da imagem ID ' . $imgId
                );
                $log->update(['status' => 'FAILED', 'error_message' => 'Falha na extração da imagem ID ' . $imgId]);
                return;
            }

            $extractedWorkouts[$imgId] = $result['extracted_data'] ?? [];
            $data['extracted_workouts'] = $extractedWorkouts;
            $log->update(['structured_json' => $data]);
        }

        // --- 2. CONSOLIDAÇÃO ---
        $log->update(['status' => 'CONSOLIDATING']);
        
        $consolidatorPayload = "Abaixo estão os dados estruturados extraídos de cada página de treino. Consolide-os em um único plano de treino semanal.\n\n" . json_encode($extractedWorkouts);
        
        $consolidatorResult = $this->consolidator->execute($user, $consolidatorPayload, [
            'clinic_id' => $user->clinic_id,
            'clinicId' => $user->academy_company_id,
        ]);

        if (!$consolidatorResult['ok']) {
            $this->scanLearning->recordFailure(
                log: $log,
                failedStage: 'consolidation',
                errorType: 'consolidator_failed',
                aiIdentifiedContent: ['extracted_workouts' => $extractedWorkouts, 'result' => $consolidatorResult],
                expectedResult: ['consolidated_workout' => 'weekly_workout_plan'],
                errorMessage: $consolidatorResult['error'] ?? 'Falha na consolidacao dos treinos.'
            );
            $log->update(['status' => 'FAILED', 'error_message' => 'Falha na consolidação dos treinos.']);
            return;
        }

        $data['consolidated_workout'] = $consolidatorResult['consolidated_data'] ?? null;
        if ($this->consolidatedIsEmpty($data['consolidated_workout'])) {
            $data['consolidated_workout'] = $this->fallbackConsolidatedWorkout($extractedWorkouts);
        }
        $log->update(['structured_json' => $data]);

        // --- 3. AUDITORIA ---
        $log->update(['status' => 'AUDITING']);

        $auditorPayload = "Por favor, audite o seguinte plano de treino unificado:\n\n" . json_encode($data['consolidated_workout']);

        $auditorResult = $this->auditor->execute($user, $auditorPayload, [
            'clinic_id' => $user->clinic_id,
            'clinicId' => $user->academy_company_id,
        ]);

        if (!$auditorResult['ok']) {
            $this->scanLearning->recordFailure(
                log: $log,
                failedStage: 'audit',
                errorType: 'auditor_failed',
                aiIdentifiedContent: ['consolidated_workout' => $data['consolidated_workout'], 'result' => $auditorResult],
                expectedResult: ['audit_results' => 'structural_audit_json'],
                errorMessage: $auditorResult['error'] ?? 'Falha na auditoria estrutural do treino.'
            );
            $log->update(['status' => 'FAILED', 'error_message' => 'Falha na auditoria estrutural do treino.']);
            return;
        }

        $data['audit_results'] = $auditorResult['audit_data'] ?? null;
        
        $log->update([
            'status' => 'WAITING_REVIEW',
            'structured_json' => $data
        ]);
    }

    private function safeAiErrorMessage(string $message): string
    {
        $lowered = strtolower($message);

        if (
            str_contains($lowered, 'openai') ||
            str_contains($lowered, 'api key') ||
            str_contains($lowered, 'api_key') ||
            str_contains($lowered, 'sk-') ||
            str_contains($lowered, 'token') ||
            str_contains($lowered, 'billing') ||
            str_contains($lowered, 'quota')
        ) {
            return 'Servico de IA temporariamente indisponivel. Verifique a configuracao da chave da OpenAI.';
        }

        return $message;
    }

    private function consolidatedIsEmpty(mixed $payload): bool
    {
        if (! is_array($payload)) {
            return true;
        }

        $consolidated = $payload['consolidated_workout'] ?? $payload['days'] ?? null;
        if (is_array($consolidated)) {
            foreach ($consolidated as $dayPayload) {
                if (is_array($dayPayload)) {
                    $exercises = $dayPayload['exercises'] ?? $dayPayload['exercicios'] ?? $dayPayload;
                    if (is_array($exercises) && count($exercises) > 0) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function fallbackConsolidatedWorkout(array $extractedWorkouts): array
    {
        $days = [
            'segunda-feira',
            'terça-feira',
            'quarta-feira',
            'quinta-feira',
            'sexta-feira',
            'sábado',
            'domingo',
        ];

        $consolidated = [];
        $workoutName = 'Treino Semanal Importado';

        foreach (array_values($extractedWorkouts) as $index => $workout) {
            if (! is_array($workout)) {
                continue;
            }

            $day = $workout['day'] ?? $days[$index] ?? 'segunda-feira';
            if (! is_string($day) || $day === '') {
                $day = $days[$index] ?? 'segunda-feira';
            }

            $exercises = $workout['exercises'] ?? $workout['exercicios'] ?? [];
            if (! is_array($exercises) || count($exercises) === 0) {
                continue;
            }

            $workoutName = (string) ($workout['workout_name'] ?? $workoutName);
            $consolidated[$day] = array_values($exercises);
        }

        return [
            'workout_name' => $workoutName,
            'days_present' => array_keys($consolidated),
            'days_missing' => array_values(array_diff($days, array_keys($consolidated))),
            'consolidated_workout' => $consolidated,
            'fallback_used' => true,
            'fallback_reason' => 'Consolidador retornou vazio; exercícios extraídos foram preservados automaticamente.',
        ];
    }

    private function isAiServiceError(string $message): bool
    {
        $lowered = strtolower($message);

        return str_contains($lowered, 'openai') ||
            str_contains($lowered, 'api key') ||
            str_contains($lowered, 'api_key') ||
            str_contains($lowered, 'sk-') ||
            str_contains($lowered, 'token') ||
            str_contains($lowered, 'billing') ||
            str_contains($lowered, 'quota') ||
            str_contains($lowered, 'rate limit') ||
            str_contains($lowered, '429') ||
            str_contains($lowered, '401') ||
            str_contains($lowered, '403');
    }
}

/**
 * Função helper interna para arredondar porcentagens
 */
if (!function_exists('App\Services\AI\Math_round')) {
    function Math_round($val) {
        return (int) round($val);
    }
}
