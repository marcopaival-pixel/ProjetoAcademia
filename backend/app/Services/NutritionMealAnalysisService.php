<?php

namespace App\Services;

use App\Models\User;
use App\Services\AI\AIProviderService;

class NutritionMealAnalysisService
{
    public function __construct(
        private AIProviderService $aiProvider,
    ) {}

    public function analyze(User $user, string $description, string $mealType = 'snack'): array
    {
        $description = trim($description);

        if ($description === '') {
            return $this->fallback('', $mealType, 'Descreva a refeicao para a IA estimar os macros.');
        }

        $messages = [
            [
                'role' => 'system',
                'content' => implode("\n", [
                    'Voce e um nutricionista esportivo. Analise refeicoes descritas em portugues do Brasil.',
                    'Retorne exclusivamente JSON valido com estes campos:',
                    'food_name string curta',
                    'amount number',
                    'unit string',
                    'calories integer',
                    'protein_g number',
                    'carbs_g number',
                    'fat_g number',
                    'confidence number de 0 a 1',
                    'notes string curta explicando se e estimativa',
                    'Nao inclua markdown.',
                ]),
            ],
            [
                'role' => 'user',
                'content' => "Refeicao ({$mealType}): {$description}",
            ],
        ];

        $result = $this->aiProvider->call($user, $messages, 'nutrition_meal_text_parser', 'fast', [
            'temperature' => 0.1,
            'max_tokens' => 400,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!($result['ok'] ?? false)) {
            return $this->fallback($description, $mealType, $result['error'] ?? null);
        }

        $decoded = json_decode($this->cleanJson((string) ($result['message'] ?? '')), true);
        if (!is_array($decoded)) {
            return $this->fallback($description, $mealType, 'A IA retornou uma resposta fora do formato esperado.');
        }

        return $this->normalize($decoded, $description, $mealType, 'ai');
    }

    private function fallback(string $description, string $mealType, ?string $reason = null): array
    {
        $text = mb_strtolower($description);
        $items = [
            'frango' => [165, 31, 0, 4],
            'ovo' => [78, 6, 1, 5],
            'arroz' => [130, 3, 28, 0],
            'feijao' => [76, 5, 14, 1],
            'banana' => [89, 1, 23, 0],
            'pao' => [135, 5, 25, 2],
            'salada' => [25, 1, 5, 0],
            'batata' => [86, 2, 20, 0],
            'carne' => [250, 26, 0, 15],
            'whey' => [120, 24, 3, 2],
        ];

        $calories = 0;
        $protein = 0.0;
        $carbs = 0.0;
        $fat = 0.0;

        foreach ($items as $keyword => [$kcal, $p, $c, $f]) {
            if (str_contains($text, $keyword)) {
                $calories += $kcal;
                $protein += $p;
                $carbs += $c;
                $fat += $f;
            }
        }

        if ($calories === 0) {
            $calories = 350;
            $protein = 20.0;
            $carbs = 35.0;
            $fat = 12.0;
        }

        return $this->normalize([
            'food_name' => $description !== '' ? $description : 'Refeicao estimada',
            'amount' => 1,
            'unit' => 'refeicao',
            'calories' => $calories,
            'protein_g' => $protein,
            'carbs_g' => $carbs,
            'fat_g' => $fat,
            'confidence' => 0.45,
            'notes' => $reason ? "Estimativa local: {$reason}" : 'Estimativa local baseada na descricao.',
        ], $description, $mealType, 'local_estimate');
    }

    private function normalize(array $data, string $description, string $mealType, string $source): array
    {
        return [
            'meal_type' => $mealType,
            'food_name' => mb_substr((string) ($data['food_name'] ?? $description ?: 'Refeicao analisada'), 0, 120),
            'amount' => (float) ($data['amount'] ?? 1),
            'unit' => (string) ($data['unit'] ?? 'refeicao'),
            'calories' => max(0, (int) round((float) ($data['calories'] ?? 0))),
            'protein_g' => round(max(0, (float) ($data['protein_g'] ?? 0)), 1),
            'carbs_g' => round(max(0, (float) ($data['carbs_g'] ?? 0)), 1),
            'fat_g' => round(max(0, (float) ($data['fat_g'] ?? 0)), 1),
            'confidence' => max(0, min(1, (float) ($data['confidence'] ?? 0.5))),
            'notes' => (string) ($data['notes'] ?? 'Valores estimados. Confira antes de salvar.'),
            'source' => $source,
        ];
    }

    private function cleanJson(string $content): string
    {
        $content = preg_replace('/```json\s?/', '', $content) ?? $content;
        $content = preg_replace('/```/', '', $content) ?? $content;

        return trim($content);
    }
}
