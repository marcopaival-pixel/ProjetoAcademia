<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FoodEntryResource;
use App\Models\FoodEntry;
use App\Models\UserProfile;
use App\Models\WeightEntry;
use App\Services\Nutrition;
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
            'targets' => $this->nutritionTargets($user),
            'entries' => FoodEntryResource::collection($entries)->resolve(),
        ]);
    }

    public function updateGoal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'goal' => ['required', 'in:lose,lose_aggressive,recomp,maintain,gain,performance'],
            'split' => ['required', 'in:cutting,bulking,maintenance'],
        ]);

        $user = $request->user();
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        $latestWeight = WeightEntry::where('user_id', $user->id)
            ->orderByDesc('weighed_at')
            ->value('weight_kg');

        $calc = Nutrition::estimateTarget(
            (string) $profile->birth_date,
            (int) $profile->height_cm,
            $profile->sex ?: 'M',
            $profile->activity_level ?: 'moderate',
            $data['goal'],
            (float) $latestWeight
        );

        $kcal = $calc['ok'] ? $calc['target'] : ($profile->daily_calorie_target ?? 2000);
        $macros = match ($data['split']) {
            'cutting' => ['p' => 0.40, 'c' => 0.35, 'f' => 0.25],
            'bulking' => ['p' => 0.25, 'c' => 0.55, 'f' => 0.20],
            'maintenance' => ['p' => 0.30, 'c' => 0.40, 'f' => 0.30],
        };

        $profile->update([
            'goal' => $data['goal'],
            'daily_calorie_target' => $kcal,
            'protein_target_g' => round(($kcal * $macros['p']) / 4, 1),
            'carbs_target_g' => round(($kcal * $macros['c']) / 4, 1),
            'fat_target_g' => round(($kcal * $macros['f']) / 9, 1),
        ]);

        return $this->success([
            'message' => 'Estrategia nutricional atualizada com sucesso.',
            'targets' => $this->nutritionTargets($user),
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

    /**
     * @return array<string, mixed>
     */
    private function nutritionTargets($user): array
    {
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);

        return [
            'goal' => $profile->goal ?? 'maintain',
            'calories' => $profile->daily_calorie_target !== null ? (int) $profile->daily_calorie_target : null,
            'protein_g' => $profile->protein_target_g !== null ? (float) $profile->protein_target_g : null,
            'carbs_g' => $profile->carbs_target_g !== null ? (float) $profile->carbs_target_g : null,
            'fat_g' => $profile->fat_target_g !== null ? (float) $profile->fat_target_g : null,
        ];
    }
}
