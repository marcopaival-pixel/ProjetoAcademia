<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FoodEntry;
use App\Services\Nutrition\NutritionAIEngine;
use App\Services\NutritionMealAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NutritionDiaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $date = $request->query('date', now()->toDateString());

        $entries = FoodEntry::query()
            ->where('user_id', $user->id)
            ->whereDate('entry_date', $date)
            ->orderBy('id')
            ->get();

        $totals = [
            'calories' => (int) $entries->sum('calories'),
            'protein_g' => round((float) $entries->sum('protein_g'), 1),
            'carbs_g' => round((float) $entries->sum('carbs_g'), 1),
            'fat_g' => round((float) $entries->sum('fat_g'), 1),
        ];

        return response()->json([
            'data' => [
                'date' => $date,
                'totals' => $totals,
                'targets' => $this->targets($user),
                'entries' => $entries->map(fn (FoodEntry $entry) => $this->entryPayload($entry))->values(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedEntry($request);
        $entry = FoodEntry::create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['data' => $this->entryPayload($entry)], 201);
    }

    public function update(Request $request, FoodEntry $entry): JsonResponse
    {
        abort_unless($entry->user_id === $request->user()->id, 404);

        $entry->update($this->validatedEntry($request));

        return response()->json(['data' => $this->entryPayload($entry->refresh())]);
    }

    public function destroy(Request $request, FoodEntry $entry): JsonResponse
    {
        abort_unless($entry->user_id === $request->user()->id, 404);
        $entry->delete();

        return response()->json(['data' => ['deleted' => true]]);
    }

    public function analyzeMeal(Request $request, NutritionMealAnalysisService $service): JsonResponse
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'min:3', 'max:1000'],
            'meal_type' => ['nullable', Rule::in(['breakfast', 'lunch', 'dinner', 'snack', 'other'])],
        ]);

        return response()->json([
            'data' => $service->analyze(
                $request->user(),
                $data['description'],
                $data['meal_type'] ?? 'snack',
            ),
        ]);
    }

    public function analyzePhoto(Request $request, NutritionAIEngine $engine): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:5120'],
            'meal_type' => ['nullable', Rule::in(['breakfast', 'lunch', 'dinner', 'snack', 'other'])],
        ]);

        $result = $engine->analyzePhoto($request->user(), $request->file('photo'));

        if (! ($result['success'] ?? false)) {
            return response()->json([
                'message' => $result['error'] ?? 'Nao foi possivel analisar a foto.',
                'errors' => ['photo' => $result['warnings'] ?? []],
            ], (($result['code'] ?? null) === 'credits_exceeded') ? 402 : 422);
        }

        $foods = collect($result['foods'] ?? []);
        $first = $foods->first() ?? [];
        $foodName = $foods->pluck('name')->filter()->join(', ');

        return response()->json([
            'data' => [
                'meal_type' => $request->input('meal_type', 'snack'),
                'food_name' => $foodName !== '' ? $foodName : ($first['name'] ?? 'Refeicao identificada'),
                'amount' => null,
                'unit' => 'refeicao',
                'calories' => (int) $foods->sum('kcal'),
                'protein_g' => round((float) $foods->sum('p'), 1),
                'carbs_g' => round((float) $foods->sum('c'), 1),
                'fat_g' => round((float) $foods->sum('f'), 1),
                'confidence' => (float) ($result['confidence'] ?? 0.86),
                'notes' => implode(' ', $result['warnings'] ?? []),
                'source' => 'photo',
            ],
        ]);
    }

    private function validatedEntry(Request $request): array
    {
        return $request->validate([
            'entry_date' => ['required', 'date'],
            'meal_type' => ['required', Rule::in(['breakfast', 'lunch', 'dinner', 'snack', 'other'])],
            'food_name' => ['required', 'string', 'max:120'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:30'],
            'calories' => ['required', 'integer', 'min:0', 'max:20000'],
            'protein_g' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'carbs_g' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'fat_g' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ]);
    }

    private function entryPayload(FoodEntry $entry): array
    {
        return [
                'id' => $entry->id,
                'meal_type' => $entry->meal_type,
                'food_name' => $entry->food_name,
                'amount' => $entry->amount,
                'unit' => $entry->unit,
                'calories' => $entry->calories,
                'protein_g' => $entry->protein_g,
                'carbs_g' => $entry->carbs_g,
                'fat_g' => $entry->fat_g,
                'entry_date' => optional($entry->entry_date)->toDateString(),
        ];
    }

    private function targets($user): array
    {
        $profile = $user->profile ?? null;

        return [
            'goal' => $profile?->goal ?? 'maintain',
            'calories' => $profile?->daily_calorie_target,
            'protein_g' => null,
            'carbs_g' => null,
            'fat_g' => null,
        ];
    }
}
