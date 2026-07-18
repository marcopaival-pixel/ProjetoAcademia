<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FoodEntry;
use App\Models\MealTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MealTemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $templates = MealTemplate::with('items')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'data' => [
                'templates' => $templates
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.meal_type' => ['required', Rule::in(['breakfast', 'lunch', 'dinner', 'snack', 'other'])],
            'items.*.food_name' => ['required', 'string', 'max:120'],
            'items.*.calories' => ['required', 'integer', 'min:0'],
            'items.*.protein_g' => ['nullable', 'numeric', 'min:0'],
            'items.*.carbs_g' => ['nullable', 'numeric', 'min:0'],
            'items.*.fat_g' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $template = MealTemplate::create([
                'user_id' => $request->user()->id,
                'name' => $validated['name'],
            ]);

            foreach ($validated['items'] as $index => $itemData) {
                $template->items()->create([
                    'meal_type' => $itemData['meal_type'],
                    'food_name' => $itemData['food_name'],
                    'calories' => $itemData['calories'],
                    'protein_g' => $itemData['protein_g'] ?? null,
                    'carbs_g' => $itemData['carbs_g'] ?? null,
                    'fat_g' => $itemData['fat_g'] ?? null,
                    'position' => $index,
                ]);
            }
            DB::commit();

            $template->load('items');

            return response()->json(['data' => $template], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro interno ao criar o template de refeição.'], 500);
        }
    }

    public function apply(Request $request, MealTemplate $template): JsonResponse
    {
        abort_unless($template->user_id === $request->user()->id, 404);

        $date = $request->input('entry_date', now()->toDateString());

        DB::beginTransaction();
        try {
            $entries = [];
            foreach ($template->items as $item) {
                $entry = FoodEntry::create([
                    'user_id' => $request->user()->id,
                    'entry_date' => $date,
                    'meal_type' => $item->meal_type,
                    'food_name' => $item->food_name,
                    'calories' => $item->calories,
                    'protein_g' => $item->protein_g,
                    'carbs_g' => $item->carbs_g,
                    'fat_g' => $item->fat_g,
                ]);
                $entries[] = $entry;
            }
            DB::commit();

            return response()->json([
                'message' => count($entries) . ' itens adicionados ao diário.',
                'data' => $entries,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro interno ao aplicar o template.'], 500);
        }
    }
}
