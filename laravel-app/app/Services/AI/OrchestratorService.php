<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AIOrchestratorLog;
use App\Services\MonetizationService;
use App\Services\AI\Agents\TrainingAgent;
use App\Services\AI\Agents\NutritionAgent;
use App\Services\AI\Agents\ClinicalAgent;
use App\Services\AI\Agents\SupportAgent;
use App\Services\AI\Agents\AnalyticsAgent;
use App\Services\AI\Agents\FinanceAgent;
use App\Services\AI\Agents\SalesAgent;
use App\Services\AI\Agents\RetentionAgent;
use App\Services\AI\Agents\VisionAgent;
use Exception;
use Illuminate\Support\Facades\Log;

class OrchestratorService
{
    public function __construct(
        private IntentClassifierService $classifier,
        private MonetizationService $monetization
    ) {}

    /**
     * Ponto de entrada principal para orquestração de IA
     */
    public function run(User $user, string $message, array $context = []): array
    {
        $startTime = microtime(true);
        $clinicId = $context['clinic_id'] ?? $user->clinic_id ?? null;
        $intent = 'support';

        try {
            // 1. Validação de Acesso (Monetização e Plano)
            $access = $this->monetization->checkAccess($user, 'ai_orchestrator');
            if (!$access['allowed']) {
                $reason = $access['message'] ?? 'Limite de IA atingido.';
                $this->logAccessDenied($user, $intent, $message, $reason, $clinicId);

                return [
                    'status' => 'limit_reached',
                    'message' => $reason,
                    'intent' => $intent
                ];
            }

            // 2. Fluxo de Visão (Se houver imagem)
            if (!empty($context['image_path']) || !empty($context['image_base64']) || !empty($context['image_url'])) {
                $visionAgent = app(VisionAgent::class);
                $visionResult = $visionAgent->execute($user, $message, $context);

                if ($visionResult['ok']) {
                    $structuredData = $visionResult['structured_data'] ?? [];
                    $intent = $structuredData['document_type'] ?? $intent;
                    
                    // Se a visão resolveu o problema (ex: foto de progresso), podemos retornar direto
                    if ($intent === 'body_progress_photo') {
                        return array_merge(['status' => 'success', 'intent' => $intent], $visionResult);
                    }

                    // Caso contrário, injetamos os dados estruturados no contexto para o agente de domínio
                    $context['vision_data'] = $structuredData;
                    $message .= "\n[DADOS EXTRAÍDOS DA IMAGEM]: " . json_encode($structuredData);
                }
            }

            // 3. Classificação de Intenção (Se ainda não definida pela visão)
            if ($intent === 'support' || empty($context['vision_data'])) {
                $intent = $context['intent'] ?? $this->classifier->classify($message);
            }
            $context['clinic_id'] = $clinicId;

            // 4. Seleção e Execução do Agente de Domínio
            $agent = $this->resolveAgent($intent);
            $result = $agent->execute($user, $message, $context);

            // 4. Registro de Uso de Cota (Monetização)
            $this->monetization->logUsage($user, 'ai_orchestrator');

            // 5. Retorno Estruturado
            return array_merge([
                'status' => $result['ok'] ? 'success' : 'error',
                'intent' => $intent
            ], $result);

        } catch (Exception $e) {
            Log::error("Erro no NexShape Orchestrator: " . $e->getMessage());
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'intent' => $intent
            ];
        }
    }

    /**
     * Resolve a instância do agente baseado na intenção
     */
    private function resolveAgent(string $intent)
    {
        return match ($intent) {
            'training' => app(TrainingAgent::class),
            'nutrition' => app(NutritionAgent::class),
            'clinical' => app(ClinicalAgent::class),
            'analytics' => app(AnalyticsAgent::class),
            'finance' => app(FinanceAgent::class),
            'sales' => app(SalesAgent::class),
            'retention' => app(RetentionAgent::class),
            'vision' => app(VisionAgent::class),
            'workout_sheet' => app(TrainingAgent::class),
            'meal_photo' => app(NutritionAgent::class),
            'bioimpedance_report' => app(ClinicalAgent::class),
            'lab_exam' => app(ClinicalAgent::class),
            default => app(SupportAgent::class),
        };
    }
    /**
     * Registra o acesso negado por falta de créditos ou limites do plano.
     */
    private function logAccessDenied(User $user, string $intent, string $message, string $reason, ?int $clinicId): void
    {
        try {
            AIOrchestratorLog::create([
                'user_id' => $user->id,
                'clinic_id' => $clinicId,
                'agent_name' => $intent,
                'model_name' => 'none',
                'user_message' => $message,
                'ai_response' => 'ACCESS_DENIED: ' . $reason,
                'status' => 'limit_reached',
                'error_message' => $reason,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'total_tokens' => 0,
                'cost_usd' => 0,
                'execution_time_ms' => 0
            ]);
        } catch (Exception $e) {
            Log::error("Falha ao salvar log de acesso negado no Orchestrator: " . $e->getMessage());
        }
    }
}
