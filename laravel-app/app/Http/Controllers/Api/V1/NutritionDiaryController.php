<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FoodEntryResource;
use App\Models\FoodEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutritionDiaryController extends Controller
{
    use FormatsApiResponses;

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

        return $this->success([
            'date' => $date,
            'totals' => $totals,
            'entries' => FoodEntryResource::collection($entries)->resolve(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedEntry($request);
        $user = $request->user();

        $entry = FoodEntry::create(array_merge($data, ['user_id' => $user->id]));

        return $this->success((new FoodEntryResource($entry))->resolve(), status: 201);
    }

    public function update(Request $request, FoodEntry $foodEntry): JsonResponse
    {
        if ((int) $foodEntry->user_id !== (int) $request->user()->id) {
            return $this->error('Acesso negado.', 403, 'forbidden');
        }

        $data = $this->validatedEntry($request, false);
        $foodEntry->update($data);

        return $this->success((new FoodEntryResource($foodEntry->fresh()))->resolve());
    }

    public function destroy(Request $request, FoodEntry $foodEntry): JsonResponse
    {
        if ((int) $foodEntry->user_id !== (int) $request->user()->id) {
            return $this->error('Acesso negado.', 403, 'forbidden');
        }

        $foodEntry->delete();

        return $this->success(['deleted' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedEntry(Request $request, bool $requireDate = true): array
    {
        $rules = [
            'entry_date' => [$requireDate ? 'required' : 'sometimes', 'date'],
            'food_name' => ['required', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'in:g,ml,tbsp,tsp,cup,slice,un'],
            'calories' => ['required', 'integer', 'min:0'],
            'protein_g' => ['nullable', 'numeric', 'min:0'],
            'carbs_g' => ['nullable', 'numeric', 'min:0'],
            'fat_g' => ['nullable', 'numeric', 'min:0'],
            'meal_type' => ['required', 'in:breakfast,lunch,dinner,snack,other'],
        ];

        $validated = $request->validate($rules);

        $payload = [
            'food_name' => $validated['food_name'],
            'amount' => $validated['amount'] ?? 1,
            'unit' => $validated['unit'] ?? 'g',
            'calories' => (int) $validated['calories'],
            'protein_g' => (float) ($validated['protein_g'] ?? 0),
            'carbs_g' => (float) ($validated['carbs_g'] ?? 0),
            'fat_g' => (float) ($validated['fat_g'] ?? 0),
            'meal_type' => $validated['meal_type'],
        ];

        if (isset($validated['entry_date'])) {
            $payload['entry_date'] = $validated['entry_date'];
        } elseif ($requireDate) {
            $payload['entry_date'] = now()->toDateString();
        }

        return $payload;
    }
}
