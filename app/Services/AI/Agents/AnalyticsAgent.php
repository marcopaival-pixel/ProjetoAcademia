<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class AnalyticsAgent extends BaseAgent {
    public function getName(): string { return 'analytics'; }

    public function execute(User $user, string $message, array $context = []): array
    {
        $workoutCount = \App\Models\WorkoutSession::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $nutritionCount = \App\Models\FoodEntry::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('entry_date')
            ->count();

        $analyticsContext = "Resumo dos últimos 30 dias: ";
        $analyticsContext .= "Treinos realizados: {$workoutCount}. ";
        $analyticsContext .= "Dias com registro de alimentação: {$nutritionCount}. ";

        $systemPrompt = "Você é o Analytics Insight Engine da NexShape. Sua missão é transformar números em motivação e estratégia.
        Analise a constância do usuário e sugira ajustes para melhorar a aderência ao plano.
        Seja direto, data-driven e focado em resultados.
        Contexto de performance: {$analyticsContext}";

        return $this->aiProvider->generateResponse($systemPrompt, $message, [
            'model' => 'main',
            'user_id' => $user->id,
            'clinic_id' => $context['clinic_id'] ?? null,
            'agent_name' => $this->getName()
        ]);
    }
}
