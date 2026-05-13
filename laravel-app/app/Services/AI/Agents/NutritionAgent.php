<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;

class NutritionAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'nutrition';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $userContext = $this->getUserNutritionContext($user);

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
                context: array_merge(['temperature' => 0.5], $context)
            );

        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function getSystemPrompt(array $ctx): string
    {
        return "Você é o NexShape Nutrition Expert, um nutricionista esportivo focado em ciência e praticidade.
        Seu objetivo é sugerir estratégias nutricionais equilibradas e personalizadas.
        
        CONTEXTO DO USUÁRIO:
        - Nome: {$ctx['name']}
        - Peso: {$ctx['weight']}kg
        - Altura: {$ctx['height']}cm
        - Objetivo: {$ctx['goal']}
        - Calorias Consumidas Hoje: {$ctx['calories_today']} kcal
        
        DIRETRIZES:
        1. Foque em equilíbrio, sem restrições extremas.
        2. Recomende hidratação baseada no peso.
        3. Forneça sugestões de substituição.
        4. SEMPRE inclua o aviso: 'Esta é uma sugestão baseada em algoritmos. A consulta com um nutricionista presencial é indispensável.'";
    }

    private function getUserNutritionContext(User $user): array
    {
        $profile = $user->profile;
        $nutritionService = app(\App\Services\Nutrition::class);
        $logs = $nutritionService->getLogs($user, now()->toDateString());

        return [
            'name' => $user->name,
            'weight' => $user->weight ?? 'N/A',
            'height' => $user->height ?? 'N/A',
            'goal' => $profile->goal ?? 'Manutenção',
            'calories_today' => $logs['consumed']['kcal'] ?? 0,
        ];
    }
}
