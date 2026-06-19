<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;

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
            $subject = $this->resolveSubjectUser($user, $context);
            $userContext = $this->getUserNutritionContext($subject);
            $instructions = \Illuminate\Support\Facades\File::get(base_path('agentesprd/nutrition-agent.md'));

            // Injetar dados de visão se existirem
            if (!empty($context['vision_data'])) {
                $message = "DADOS DA REFEIÇÃO (VISÃO): " . json_encode($context['vision_data']) . "\n\nCOMENTÁRIO DO USUÁRIO: " . $message;
            }

            $messages = [
                [
                    'role' => 'system',
                    'content' => $instructions . "\n\n" . $this->getSystemContextPrompt($userContext)
                ],
                ['role' => 'user', 'content' => $message]
            ];

            $this->injectChatHistory($messages, $context);

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
        return "CONTEXTO DINÂMICO DO USUÁRIO:
        - Nome: {$ctx['name']}
        - Peso: {$ctx['weight']}kg
        - Altura: {$ctx['height']}cm
        - Objetivo: {$ctx['goal']}
        - Calorias Consumidas Hoje (já registradas): {$ctx['calories_today']} kcal";
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
