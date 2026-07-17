<?php

namespace App\Services;

use App\Services\AI\AIProviderService;
use Exception;

/**
 * @deprecated Use App\Services\AI\OrchestratorService para novos fluxos.
 */
class AIChatService
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function chat(string $userMessage, array $userMetrics = [], array $conversationHistory = []): array
    {
        try {
            $user = auth()->user();
            if (! $user) {
                return ['ok' => false, 'error' => 'Usuario autenticado nao encontrado.'];
            }

            $messages = $conversationHistory;
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            return $this->aiProvider->call($user, [
                ['role' => 'system', 'content' => $this->buildSystemPrompt($userMetrics)],
                ...$messages,
            ], 'nexbot_chat', 'fast', [
                'temperature' => 0.7,
                'max_tokens' => 500,
                'feature_key' => 'chat',
            ]);
        } catch (Exception $e) {
            return ['ok' => false, 'error' => 'Erro ao comunicar com IA: ' . $e->getMessage()];
        }
    }

    private function buildSystemPrompt(array $userMetrics): string
    {
        $metricsContext = '';

        foreach ($userMetrics as $key => $value) {
            if (is_scalar($value)) {
                $metricsContext .= "- {$key}: {$value}\n";
            }
        }

        return "Voce e o NexBot, coach contextual do NexShape. Responda com orientacao breve, segura e acionavel.\n"
            . "Nao forneca diagnostico medico, medicacao ou condutas de risco. Quando necessario, recomende acompanhamento profissional.\n\n"
            . "Contexto disponivel:\n{$metricsContext}";
    }
}
