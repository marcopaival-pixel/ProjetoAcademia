<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use App\Services\StudentContextService;
use Exception;

class TrainingAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider,
        private StudentContextService $studentContext,
    ) {}

    public function getName(): string
    {
        return 'training';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $instructions = \Illuminate\Support\Facades\File::get(base_path('../ai-agents/training-agent.md'));

            if (! empty($context['vision_data'])) {
                $message = "DADOS DA FICHA DE TREINO (VISÃO): ".json_encode($context['vision_data'])."\n\nCOMENTÁRIO DO USUÁRIO: ".$message;
            }

            $messages = [
                [
                    'role' => 'system',
                    'content' => implode("\n\n", array_filter([
                        $instructions,
                        $this->studentContext->promptBlock($user, 'training'),
                        $this->metricsPrompt($context),
                        $this->safetyGuardrailsPrompt(),
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
                modelType: 'main',
                context: array_merge(['temperature' => 0.5], $context)
            );
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
