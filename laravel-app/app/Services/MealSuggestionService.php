<?php

namespace App\Services;

use App\Models\FoodEntry;
use App\Models\User;
use App\Models\UserProfile;

class MealSuggestionService
{
    /**
     * Sugere refeição por regras + histórico do utilizador (sem LLM).
     */
    public function suggest(User $user, array $remaining): array
    {
        $profile = UserProfile::where('user_id', $user->id)->first();
        $remainingKcal = (int) ($remaining['remaining_kcal'] ?? 0);
        $remainingP = (float) ($remaining['remaining_p'] ?? 0);
        $remainingC = (float) ($remaining['remaining_c'] ?? 0);
        $remainingF = (float) ($remaining['remaining_f'] ?? 0);

        if ($remainingKcal <= 0) {
            return [
                'ok' => false,
                'error' => 'Você já atingiu ou superou a meta calórica de hoje.',
            ];
        }

        $candidates = FoodEntry::where('user_id', $user->id)
            ->select('food_name', 'calories', 'protein_g', 'carbs_g', 'fat_g', 'unit')
            ->selectRaw('count(*) as frequency')
            ->groupBy('food_name', 'calories', 'protein_g', 'carbs_g', 'fat_g', 'unit')
            ->orderByDesc('frequency')
            ->limit(30)
            ->get();

        $picked = [];
        $usedKcal = 0;
        $usedP = 0.0;
        $usedC = 0.0;
        $usedF = 0.0;
        $budget = min($remainingKcal, max(300, (int) ($remainingKcal * 0.35)));

        foreach ($candidates as $item) {
            $kcal = (int) ($item->calories ?? 0);
            if ($kcal < 1 || $kcal > $budget - $usedKcal) {
                continue;
            }

            $picked[] = $item->food_name;
            $usedKcal += $kcal;
            $usedP += (float) ($item->protein_g ?? 0);
            $usedC += (float) ($item->carbs_g ?? 0);
            $usedF += (float) ($item->fat_g ?? 0);

            if ($usedKcal >= $budget * 0.7 || count($picked) >= 4) {
                break;
            }
        }

        if ($picked === []) {
            $picked = $this->defaultFoodsForGoal($profile->goal ?? 'maintain', $budget);
            $usedKcal = (int) ($budget * 0.85);
            $usedP = round($usedKcal * 0.25 / 4, 1);
            $usedC = round($usedKcal * 0.45 / 4, 1);
            $usedF = round($usedKcal * 0.30 / 9, 1);
        }

        $mealName = $this->mealLabel();
        $message = "**{$mealName} sugerida** (~{$usedKcal} kcal)\n\n";
        $message .= implode("\n", array_map(fn ($f) => "- {$f}", $picked));
        $message .= "\n\nMacros estimados: P **{$usedP}g** · C **{$usedC}g** · G **{$usedF}g**";
        $message .= "\n\n_Restante do dia após esta refeição: ~".max(0, $remainingKcal - $usedKcal)." kcal._";

        return [
            'ok' => true,
            'message' => $message,
            'metrics' => [
                'cal' => $usedKcal,
                'p' => $usedP,
                'c' => $usedC,
                'f' => $usedF,
            ],
            'tokens' => 0,
            'model' => 'rule_engine',
        ];
    }

    private function mealLabel(): string
    {
        $hour = (int) now()->format('H');

        if ($hour < 10) {
            return 'Café da manhã';
        }
        if ($hour < 14) {
            return 'Almoço';
        }
        if ($hour < 18) {
            return 'Lanche';
        }

        return 'Jantar';
    }

    private function defaultFoodsForGoal(string $goal, int $budget): array
    {
        if ($goal === 'gain_mass' || $goal === 'hipertrofia') {
            return ['Ovos mexidos (2)', 'Aveia com whey', 'Banana'];
        }
        if ($goal === 'lose_weight' || $goal === 'emagrecimento') {
            return ['Salada com frango grelhado', 'Legumes cozidos', 'Iogurte natural'];
        }

        return ['Arroz integral', 'Feijão', 'Proteína magra (frango ou peixe)'];
    }
}
