<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AIOrchestratorLog;
use App\Services\AiCreditService;
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
use App\Services\AI\Agents\PainAgent;
use App\Services\AI\Agents\SchedulingAgent;
use App\Services\AI\Agents\PsychologyAgent;
use App\Services\AI\Agents\MedicAgent;
use App\Services\AI\Agents\ShopAgent;
use Exception;
use Illuminate\Support\Facades\Log;

class OrchestratorService
{
    public function __construct(
        private IntentClassifierService $classifier,
        private KeywordIntentRouter $keywordRouter,
        private MonetizationService $monetization,
        private AiCreditService $creditService,
    ) {}

    /**
     * Ponto de entrada principal para orquestração de IA.
     */
    public function run(User $user, string $message, array $context = []): array
    {
        $clinicId = $context['clinic_id'] ?? $context['clinicId'] ?? $user->clinic_id ?? null;
        $intent = 'support';
        $cacheKey = null;

        try {
            // 0. Cache de resposta (economia de tokens) — isolado por utilizador/tenant
            $context['user_id'] = $user->id;
            if (empty($context['force_ia']) && ! AiCreditService::shouldSkipResponseCache($context)) {
                $cacheKey = AiCreditService::buildCacheKey($message, $context, $user->id);
                $cached = $this->creditService->getCachedResponse($cacheKey);
                if ($cached !== null) {
                    return [
                        'status' => 'success',
                        'message' => $cached,
                        'intent' => $context['intent'] ?? 'cached',
                        'cached' => true,
                        'tokens' => 0,
                        'cost' => 0,
                    ];
                }
            }

            // 1. Validação de Acesso (Monetização e Plano)
            $access = $this->monetization->checkAccess($user, 'ai_orchestrator');
            if (! $access['allowed'] && ! $user->isAdministrator()) {
                $reason = $access['message'] ?? 'Limite de IA atingido.';
                $this->logAccessDenied($user, $intent, $message, $reason, $clinicId);

                return [
                    'status' => 'limit_reached',
                    'message' => $reason,
                    'intent' => $intent,
                ];
            }

            // 2. Limite USD diário (governança)
            if ($this->exceedsDailyBudget($clinicId)) {
                $reason = 'Limite diário de custo de IA atingido para esta clínica.';
                $this->logAccessDenied($user, $intent, $message, $reason, $clinicId);

                return [
                    'status' => 'limit_reached',
                    'message' => $reason,
                    'intent' => $intent,
                ];
            }

            // 3. Fluxo de Visão (Se houver imagem)
            if (! empty($context['image_path']) || ! empty($context['image_base64']) || ! empty($context['image_url'])) {
                $visionAgent = app(VisionAgent::class);
                $visionResult = $visionAgent->execute($user, $message, $context);

                if ($visionResult['ok']) {
                    $structuredData = $visionResult['structured_data'] ?? [];
                    $intent = $structuredData['document_type'] ?? $intent;

                    if ($intent === 'body_progress_photo') {
                        return array_merge(['status' => 'success', 'intent' => $intent], $visionResult);
                    }

                    $context['vision_data'] = $structuredData;
                    $message .= "\n[DADOS EXTRAÍDOS DA IMAGEM]: ".json_encode($structuredData);
                }
            }

            // 4. Resolução de intenção: explícita → keywords → LLM (fallback)
            $intent = $this->resolveIntent($message, $context, $intent);
            $context['clinic_id'] = $clinicId;
            $context['resolved_intent'] = $intent;

            // 5. Verificar créditos antes de agentes que consomem LLM
            $featureCode = $context['feature_code'] ?? config('ai.default_feature_code', 'ai_chat');
            if ($this->agentRequiresCredits($intent) && ! $user->isAdministrator()) {
                if (! $this->creditService->hasCredits($user, $featureCode)) {
                    $reason = 'Créditos de IA insuficientes para esta operação.';
                    $this->logAccessDenied($user, $intent, $message, $reason, $clinicId);

                    return [
                        'status' => 'limit_reached',
                        'message' => $reason,
                        'intent' => $intent,
                    ];
                }
            }

            // 6. Seleção e Execução do Agente de Domínio
            $agent = $this->resolveAgent($intent);
            $result = $agent->execute($user, $message, $context);

            if (! ($result['ok'] ?? false)) {
                return [
                    'status' => 'error',
                    'error' => $result['error'] ?? 'Erro desconhecido no agente.',
                    'intent' => $intent,
                ];
            }

            // 7. Debitar créditos e registrar uso (billing unificado)
            $tokensUsed = (int) ($result['tokens'] ?? 0);
            if ($tokensUsed > 0 && ! $user->isAdministrator()) {
                $this->creditService->consume($user, $featureCode, [
                    'intent' => $intent,
                    'source' => $context['source'] ?? 'orchestrator',
                    'tokens' => $tokensUsed,
                ], $cacheKey);
            }

            // 8. Cache de resposta bem-sucedida (sem PII de terceiros)
            if ($cacheKey && ! empty($result['message']) && $tokensUsed > 0 && ! AiCreditService::shouldSkipResponseCache($context)) {
                $this->creditService->cacheResponse(
                    $cacheKey,
                    $result['message'],
                    config('ai.response_cache_ttl', 86400)
                );
            }

            // 9. Registro de Uso de Cota (Monetização)
            $this->monetization->logUsage($user, 'ai_orchestrator');

            return array_merge([
                'status' => 'success',
                'intent' => $intent,
            ], $result);

        } catch (Exception $e) {
            Log::error('Erro no NexShape Orchestrator: '.$e->getMessage());

            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'intent' => $intent,
            ];
        }
    }

    private function resolveIntent(string $message, array $context, string $currentIntent): string
    {
        if (! empty($context['intent'])) {
            return $context['intent'];
        }

        if ($currentIntent !== 'support' && ! empty($context['vision_data'])) {
            return $currentIntent;
        }

        $keywordIntent = $this->keywordRouter->resolve($message);
        if ($keywordIntent !== null) {
            return $keywordIntent;
        }

        if (config('ai.llm_classifier_enabled', true)) {
            return $this->classifier->classify($message);
        }

        return 'support';
    }

    private function agentRequiresCredits(string $intent): bool
    {
        // Agentes baseados em regras (sem LLM) — não debitam créditos
        $noCreditIntents = array_merge(
            config('ai.disabled_intents', ['finance', 'sales', 'retention']),
            ['analytics']
        );

        return ! in_array($intent, $noCreditIntents, true);
    }

    private function exceedsDailyBudget(?int $clinicId): bool
    {
        $globalLimit = (float) config('ai.limits.daily_usd_global', 0);
        $clinicLimit = (float) config('ai.limits.daily_usd_per_clinic', 0);

        if ($globalLimit > 0) {
            $todayGlobal = (float) AIOrchestratorLog::whereDate('created_at', today())->sum('cost_usd');
            if ($todayGlobal >= $globalLimit) {
                return true;
            }
        }

        if ($clinicLimit > 0 && $clinicId) {
            $todayClinic = (float) AIOrchestratorLog::where('clinic_id', $clinicId)
                ->whereDate('created_at', today())
                ->sum('cost_usd');
            if ($todayClinic >= $clinicLimit) {
                return true;
            }
        }

        return false;
    }

    private function resolveAgent(string $intent)
    {
        return match ($intent) {
            'training', 'workout_sheet'             => app(TrainingAgent::class),
            'nutrition', 'meal_photo'               => app(NutritionAgent::class),
            'clinical', 'bioimpedance_report',
            'lab_exam'                              => app(ClinicalAgent::class),
            'pain', 'pain_tracking', 'eva'          => app(PainAgent::class),
            'scheduling', 'appointment',
            'agenda'                               => app(SchedulingAgent::class),
            'psychology', 'mental_health', 'mood'  => app(PsychologyAgent::class),
            'medic', 'prescription', 'medication'  => app(MedicAgent::class),
            'shop', 'product', 'order',
            'cart', 'wishlist', 'points'           => app(ShopAgent::class),
            'analytics'                            => app(AnalyticsAgent::class),
            'finance'                              => app(FinanceAgent::class),
            'sales'                                => app(SalesAgent::class),
            'retention'                            => app(RetentionAgent::class),
            'vision'                               => app(VisionAgent::class),
            default                                => app(SupportAgent::class),
        };
    }

    private function logAccessDenied(User $user, string $intent, string $message, string $reason, ?int $clinicId): void
    {
        try {
            AIOrchestratorLog::create([
                'user_id' => $user->id,
                'clinic_id' => $clinicId,
                'agent_name' => $intent,
                'model_name' => 'none',
                'user_message' => $message,
                'ai_response' => 'ACCESS_DENIED: '.$reason,
                'status' => 'limit_reached',
                'error_message' => $reason,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'total_tokens' => 0,
                'cost_usd' => 0,
                'execution_time_ms' => 0,
            ]);
        } catch (Exception $e) {
            Log::error('Falha ao salvar log de acesso negado no Orchestrator: '.$e->getMessage());
        }
    }
}
