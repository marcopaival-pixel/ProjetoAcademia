<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;

class SupportAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'support';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => implode("\n\n", array_filter([
                        'Voce e o NexShape Support Assistant. Ajude o usuario com duvidas sobre como usar a plataforma NexShape. Seja gentil, direto e eficiente.',
                        $this->metricsPrompt($context),
                        $this->actionContractPrompt(),
                    ])),
                ],
                ...$this->conversationMessages($context),
                ['role' => 'user', 'content' => $message],
            ];

            return $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'fast',
                context: array_merge(['temperature' => 0.3], $context)
            );
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
