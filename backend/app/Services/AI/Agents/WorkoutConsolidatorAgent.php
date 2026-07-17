<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

class WorkoutConsolidatorAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'workout_consolidator';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $systemPrompt = File::get(base_path('../ai-agents/workout-consolidator-agent.md'));

            $messages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $message ?: 'Consolide os treinos fornecidos no contexto.'
                ]
            ];

            $response = $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'workout_import',
                context: array_merge($context, ['response_format' => ['type' => 'json_object']])
            );

            if ($response['ok']) {
                $response['consolidated_data'] = json_decode($response['message'], true);
            }

            return $response;

        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
