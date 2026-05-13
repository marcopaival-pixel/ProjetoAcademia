<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;

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
            // Coletar contexto de treino real
            $userContext = $this->getUserTrainingContext($user);

            $messages = [
                [
                    'role' => 'system',
                    'content' => $this->getSystemPrompt($userContext)
                ],
                ['role' => 'user', 'content' => $message]
            ];

            return $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'main',
                context: $context
            );

        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function getSystemPrompt(array $ctx): string
    {
        return "Você é o NexShape Performance Coach, um especialista em fisiologia do exercício e biomecânica.
        Seu objetivo é gerar prescrições de treino seguras e eficazes.
        
        CONTEXTO DO ALUNO:
        - Nome: {$ctx['name']}
        - Objetivo: {$ctx['goal']}
        - Nível: {$ctx['level']}
        - Último Treino: {$ctx['last_workout']}
        
        DIRETRIZES:
        1. Priorize a segurança articular.
        2. Use linguagem técnica mas motivadora.
        3. Se houver lesão, sugira alternativas.
        4. Retorne o treino formatado em Markdown.";
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
