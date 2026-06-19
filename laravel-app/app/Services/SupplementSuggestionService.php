<?php

namespace App\Services;

use App\Models\SupplementCatalog;

class SupplementSuggestionService
{
    /**
     * Sugestão de stack por catálogo + regras (sem LLM).
     */
    public function suggest(string $goal = 'geral'): array
    {
        $goalLower = mb_strtolower($goal);
        $categoryHints = $this->categoryHintsForGoal($goalLower);

        $query = SupplementCatalog::where('is_active', true);

        if ($categoryHints !== []) {
            $query->where(function ($q) use ($categoryHints) {
                foreach ($categoryHints as $hint) {
                    $q->orWhere('category', 'like', "%{$hint}%")
                        ->orWhere('benefits', 'like', "%{$hint}%")
                        ->orWhere('name', 'like', "%{$hint}%");
                }
            });
        }

        $items = $query->limit(4)->get();

        if ($items->isEmpty()) {
            $items = SupplementCatalog::where('is_active', true)->limit(3)->get();
        }

        $supplements = $items->map(fn ($s) => [
            'name' => $s->name,
            'dosage' => $s->default_dosage ?? '1',
            'unit' => $s->default_unit ?? 'dose',
            'frequency' => 'diário',
            'time_of_day' => 'manhã',
            'goal' => $goal,
            'observations' => mb_substr((string) ($s->benefits ?? $s->description ?? ''), 0, 120),
        ])->values()->all();

        return [
            'ok' => true,
            'suggestion' => [
                'stack_name' => 'Stack '.ucfirst($goal ?: 'Geral'),
                'goal' => $goal,
                'supplements' => $supplements,
            ],
            'message' => 'Stack sugerido com base no catálogo de suplementos e no seu objetivo.',
            'tokens' => 0,
            'model' => 'rule_engine',
        ];
    }

    private function categoryHintsForGoal(string $goal): array
    {
        if (str_contains($goal, 'massa') || str_contains($goal, 'hipertrofia') || str_contains($goal, 'ganho')) {
            return ['creatina', 'whey', 'proteína', 'hipertrofia'];
        }
        if (str_contains($goal, 'emagrec') || str_contains($goal, 'cut') || str_contains($goal, 'perda')) {
            return ['termogênico', 'omega', 'multivitamin'];
        }
        if (str_contains($goal, 'energia') || str_contains($goal, 'performance')) {
            return ['cafeína', 'beta-alanina', 'energia'];
        }
        if (str_contains($goal, 'sono') || str_contains($goal, 'recuper')) {
            return ['magnésio', 'zinco', 'melatonina'];
        }

        return ['multivitamin', 'omega'];
    }
}
