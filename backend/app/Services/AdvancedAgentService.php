<?php

namespace App\Services;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * @deprecated Use App\Services\AI\OrchestratorService para novos fluxos.
 */
class AdvancedAgentService
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function process(User $user, string $message, array $conversationHistory = []): array
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => $this->buildAdvancedPrompt($this->collectRealContext($user))],
                ...$conversationHistory,
                ['role' => 'user', 'content' => $message],
            ];

            $result = $this->aiProvider->call($user, $messages, 'advanced_contextual_agent', 'main', [
                'temperature' => 0.4,
                'feature_key' => 'chat',
            ]);

            if (! ($result['ok'] ?? false)) {
                return $result;
            }

            $content = (string) ($result['message'] ?? '');

            return [
                'ok' => true,
                'response' => $content,
                'message' => $content,
                'action' => $this->extractJsonAction($content),
                'tokens' => $result['tokens'] ?? 0,
            ];
        } catch (Exception $e) {
            Log::error('Erro no AdvancedAgent: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function collectRealContext(User $user): array
    {
        $profile = $user->profile;

        return [
            'role' => $user->getRoleNames()[0] ?? 'aluno',
            'name' => $user->name,
            'goal' => $profile->goal ?? null,
            'features' => [
                'ai_training' => $user->hasFeature('ai_training'),
                'ai_nutrition' => $user->hasFeature('ai_nutrition'),
                'automated_actions' => $user->hasFeature('automated_actions'),
            ],
        ];
    }

    private function buildAdvancedPrompt(array $ctx): string
    {
        return "Voce e um agente contextual do NexShape. Respeite plano, permissoes e LGPD.\n"
            . "Nao execute acoes criticas sem confirmacao explicita em JSON.\n"
            . "Nao forneca diagnostico medico, medicamentos, anabolizantes ou condutas perigosas.\n"
            . "Contexto: " . json_encode($ctx, JSON_UNESCAPED_UNICODE);
    }

    private function extractJsonAction(string $content): ?array
    {
        preg_match('/\{.*\}/s', $content, $matches);
        $json = isset($matches[0]) ? json_decode($matches[0], true) : null;

        return is_array($json) ? $json : null;
    }
}
