<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;

class TrainingAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'training';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $userContext = $this->getUserTrainingContext($user);
            $instructions = \Illuminate\Support\Facades\File::get(base_path('agentesprd/training-agent.md'));

            // Injetar dados de visão se existirem
            if (!empty($context['vision_data'])) {
                $message = "DADOS DA FICHA DE TREINO (VISÃO): " . json_encode($context['vision_data']) . "\n\nCOMENTÁRIO DO USUÁRIO: " . $message;
            }

            $messages = [
                [
                    'role' => 'system',
                    'content' => $instructions . "\n\n" . $this->getSystemContextPrompt($userContext)
                ],
                ['role' => 'user', 'content' => $message]
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

    private function getSystemContextPrompt(array $ctx): string
    {
        return "CONTEXTO DINÂMICO DO ALUNO:
        - Nome: {$ctx['name']}
        - Objetivo: {$ctx['goal']}
        - Nível: {$ctx['level']}
        - Último Treino: {$ctx['last_workout']}";
    }

    private function getUserTrainingContext(User $user): array
    {
        $profile = $user->profile;
        $lastWorkout = $user->workoutSessions()->with('trainingPlan')->latest()->first();

        return [
            'name' => $user->name,
            'goal' => $profile->goal ?? 'Manutenção',
            'level' => $profile->fitness_level ?? 'Iniciante',
            'last_workout' => $lastWorkout ? $lastWorkout->trainingPlan->name : 'Nenhum registrado',
        ];
    }
}
