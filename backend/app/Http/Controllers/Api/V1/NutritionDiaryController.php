<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FoodEntry;
use App\Models\UserProfile;
use App\Services\AI\OrchestratorService;
use App\Services\AiCreditService;
use App\Services\Nutrition;
use App\Services\Nutrition\NutritionAIEngine;
use App\Services\NutritionMealAnalysisService;
use App\Services\SecureFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

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

    public function analyzeMeal(Request $request, NutritionMealAnalysisService $service, AiCreditService $credits): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'description' => ['required', 'string', 'min:3', 'max:1000'],
            'meal_type' => ['nullable', Rule::in(['breakfast', 'lunch', 'dinner', 'snack', 'other'])],
        ]);

        if (! $credits->hasCredits($user, 'nutrition_text_analysis')) {
            return response()->json([
                'message' => 'Creditos de IA insuficientes para analisar a refeicao.',
                'errors' => ['description' => ['Creditos de IA insuficientes.']],
            ], 402);
        }

        $result = $service->analyze(
            $user,
            $data['description'],
            $data['meal_type'] ?? 'snack',
        );

        if (($result['source'] ?? '') === 'ai') {
            $referenceId = hash('sha256', implode('|', [
                $user->id,
                mb_strtolower(trim($data['description'])),
                $data['meal_type'] ?? 'snack',
            ]));

            $credits->consume($user, 'nutrition_text_analysis', [
                'source' => 'api_v1_analyze_meal',
            ], $referenceId);
        }

        return response()->json([
            'data' => $result,
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

    public function suggestMeal(Request $request, OrchestratorService $orchestrator, AiCreditService $credits): JsonResponse
    {
        $user = $request->user();
        $profile = UserProfile::query()->where('user_id', $user->id)->first();

        if (! $credits->hasCredits($user, 'meal_suggestion')) {
            return response()->json([
                'success' => false,
                'code' => 'credits_exceeded',
                'error' => 'Creditos de IA insuficientes para gerar sugestao de refeicao.',
            ], 402);
        }

        $targetKcal = (int) ($profile?->daily_calorie_target ?: 2000);
        $macroTargets = Nutrition::macroTargetsForDisplay($user->hasPremiumAccess(), $profile?->toArray() ?? []);

        $todaySums = FoodEntry::query()
            ->where('user_id', $user->id)
            ->whereDate('entry_date', now()->toDateString())
            ->selectRaw('SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f')
            ->first();

        $remaining = [
            'remaining_kcal' => max($targetKcal - (float) ($todaySums->cal ?? 0), 0),
            'remaining_p' => max((float) ($macroTargets['p'] ?? 0) - (float) ($todaySums->p ?? 0), 0),
            'remaining_c' => max((float) ($macroTargets['c'] ?? 0) - (float) ($todaySums->c ?? 0), 0),
            'remaining_f' => max((float) ($macroTargets['f'] ?? 0) - (float) ($todaySums->f ?? 0), 0),
        ];

        $prompt = implode("\n", [
            'Sugira uma refeicao para o usuario com base no contexto nutricional abaixo.',
            'Nao contrarie plano prescrito por nutricionista quando houver sinais de prescricao.',
            'Responda em portugues do Brasil, com 2 ou 3 opcoes praticas e macros aproximados.',
            'Contexto: '.json_encode([
                'goal' => $profile?->goal ?? 'maintain',
                'remaining' => $remaining,
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $result = $orchestrator->run($user, $prompt, [
            'intent' => 'nutrition',
            'type' => 'meal_suggestion',
            'clinicId' => $user->academy_company_id,
            'remaining' => $remaining,
        ]);

        if (($result['status'] ?? null) !== 'success') {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Nao foi possivel gerar a sugestao.',
            ], 500);
        }

        $credits->consume($user, 'meal_suggestion', ['remaining' => $remaining]);

        return response()->json([
            'data' => [
                'suggestion' => $result['message'] ?? '',
                'remaining' => $remaining,
            ],
        ]);
    }

    public function weeklyAudit(Request $request, OrchestratorService $orchestrator, AiCreditService $credits): JsonResponse
    {
        $user = $request->user();
        $profile = UserProfile::query()->where('user_id', $user->id)->first();

        $history = FoodEntry::query()
            ->where('user_id', $user->id)
            ->whereDate('entry_date', '>=', now()->subDays(7)->toDateString())
            ->selectRaw('entry_date, SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f, GROUP_CONCAT(food_name) as foods')
            ->groupBy('entry_date')
            ->orderByDesc('entry_date')
            ->get();

        if ($history->count() < 2) {
            return response()->json([
                'success' => false,
                'error' => 'Ainda nao ha dados suficientes para uma auditoria semanal. Registre pelo menos 2 dias de alimentacao.',
            ], 422);
        }

        if (! $credits->hasCredits($user, 'diet_audit')) {
            return response()->json([
                'success' => false,
                'code' => 'credits_exceeded',
                'error' => 'Creditos de IA insuficientes para gerar a auditoria nutricional.',
            ], 402);
        }

        $summary = $history->map(fn ($day) => [
            'date' => $day->entry_date,
            'kcal' => (int) $day->cal,
            'protein_g' => round((float) $day->p, 1),
            'carbs_g' => round((float) $day->c, 1),
            'fat_g' => round((float) $day->f, 1),
            'foods' => $day->foods,
        ])->values()->all();

        $prompt = implode("\n", [
            'Audite os ultimos 7 dias de alimentacao do usuario.',
            'Se os dados forem incompletos, deixe isso claro e evite conclusoes fortes.',
            'Inclua dias registrados, media calorica, macros, pontos de atencao e recomendacoes simples.',
            'Contexto: '.json_encode([
                'goal' => $profile?->goal ?? 'maintain',
                'days' => $summary,
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $result = $orchestrator->run($user, $prompt, [
            'intent' => 'nutrition',
            'type' => 'weekly_audit',
            'clinicId' => $user->academy_company_id,
        ]);

        if (($result['status'] ?? null) !== 'success') {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Falha na auditoria.',
            ], 500);
        }

        $credits->consume($user, 'diet_audit', ['days_analyzed' => $history->count()]);

        return response()->json([
            'data' => [
                'audit' => $result['message'] ?? '',
                'days_analyzed' => $history->count(),
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

    public function uploadPhoto(Request $request, SecureFileService $secureFiles): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:5120'],
        ]);

        $user = $request->user();
        $path = $secureFiles->storeSensitiveFile($request->file('photo'), 'nutrition_photos/'.$user->id);

        return response()->json([
            'message' => 'Foto enviada com sucesso.',
            'data' => [
                'path' => $path,
            ],
        ], 201);
    }
}
