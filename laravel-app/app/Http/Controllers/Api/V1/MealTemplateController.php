<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\FoodEntry;
use App\Models\MealTemplate;
use App\Models\MealTemplateItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealTemplateController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $templates = MealTemplate::query()
            ->where('user_id', $request->user()->id)
            ->with('items')
            ->latest()
            ->get();

        return $this->success([
            'templates' => $templates->map(fn (MealTemplate $template): array => $this->templatePayload($template))->values(),
        ], [
            'count' => $templates->count(),
        ]);
    }

    public function apply(Request $request, MealTemplate $mealTemplate): JsonResponse
    {
        if ((int) $mealTemplate->user_id !== (int) $request->user()->id) {
            return $this->error('Acesso negado.', 403, 'forbidden');
        }

        $data = $request->validate([
            'entry_date' => ['required', 'date'],
        ]);

        $applied = 0;
        $mealTemplate->load('items');
        $mealTemplate->items->each(function (MealTemplateItem $item) use ($request, $data, &$applied): void {
            FoodEntry::create([
                'user_id' => $request->user()->id,
                'entry_date' => $data['entry_date'],
                'meal_type' => $item->meal_type,
                'food_name' => $item->food_name,
                'calories' => $item->calories,
                'protein_g' => $item->protein_g,
                'carbs_g' => $item->carbs_g,
                'fat_g' => $item->fat_g,
            ]);
            $applied++;
        });

        return $this->success([
            'applied' => $applied,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function templatePayload(MealTemplate $template): array
    {
        return [
            'id' => $template->id,
            'name' => $template->name,
            'professional_id' => $template->professional_id,
            'created_at' => $template->created_at?->toIso8601String(),
            'items' => $template->items->map(fn (MealTemplateItem $item): array => [
                'id' => $item->id,
                'meal_type' => $item->meal_type,
                'food_name' => $item->food_name,
                'calories' => (int) $item->calories,
                'protein_g' => (float) $item->protein_g,
                'carbs_g' => (float) $item->carbs_g,
                'fat_g' => (float) $item->fat_g,
                'position' => (int) $item->position,
            ])->values(),
        ];
    }
}
