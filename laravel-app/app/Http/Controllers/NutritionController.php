<?php

namespace App\Http\Controllers;

use App\Services\Nutrition;
use App\Services\AIChatService;
use App\Models\UserProfile;
use App\Models\FoodEntry;
use App\Models\WeightEntry;
use App\Models\Supplement;
use App\Models\WaterEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\OpenFoodFactsClient;

class NutritionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        
        $tab = $request->query('tab', 'dashboard');
        $date = $request->query('date', now()->format('Y-m-d'));
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = now()->format('Y-m-d');
        }

        // --- Basic Stats & Targets ---
        $latestWeight = WeightEntry::where('user_id', $user->id)
            ->orderByDesc('weighed_at')
            ->orderByDesc('id')
            ->value('weight_kg');

        $stats = Nutrition::estimateTarget(
            (string) $profile->birth_date,
            (int) $profile->height_cm,
            $profile->sex ?? 'M',
            $profile->activity_level ?? 'moderate',
            $profile->goal ?? 'maintain',
            (float) $latestWeight
        );

        $targetKcal = $stats['ok'] ? $stats['target'] : ($profile->daily_calorie_target ?? 2000);
        $macroTargets = Nutrition::macroTargetsForDisplay($isPremium, $profile->toArray());

        // --- Dashboard Data (Charts/Averages) ---
        $historyData = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(14)->format('Y-m-d'))
            ->selectRaw('entry_date, SUM(calories) as total_cal')
            ->groupBy('entry_date')
            ->orderBy('entry_date', 'asc')
            ->get();

        $last7Days = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(6)->format('Y-m-d'))
            ->selectRaw('entry_date, SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f')
            ->groupBy('entry_date')
            ->get();

        $averages = (object)[
            'cal' => $last7Days->avg('cal') ?? 0,
            'p' => $last7Days->avg('p') ?? 0,
            'c' => $last7Days->avg('c') ?? 0,
            'f' => $last7Days->avg('f') ?? 0,
        ];

        $consistencyCount = 0;
        foreach ($last7Days as $day) {
            $diff = abs($day->cal - $targetKcal);
            if ($diff <= ($targetKcal * 0.15)) { 
                $consistencyCount++;
            }
        }

        // --- Diary Specific Data ---
        $diaryRows = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', $date)
            ->orderBy('created_at')
            ->get();

        $todaySums = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', now()->format('Y-m-d'))
            ->selectRaw('SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f')
            ->first();

        // Specific sums for selected date ($date) in diary tab
        $selectedDateSums = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', $date)
            ->selectRaw('SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f')
            ->first();

        $remaining = (object)[
            'cal' => max($targetKcal - ($todaySums->cal ?? 0), 0),
            'p' => max(($macroTargets['p'] ?? 0) - ($todaySums->p ?? 0), 0),
            'c' => max(($macroTargets['c'] ?? 0) - ($todaySums->c ?? 0), 0),
            'f' => max(($macroTargets['f'] ?? 0) - ($todaySums->f ?? 0), 0),
        ];

        // Water
        $waterTargetMl = $profile->water_target_ml ?? 2500;
        $waterConsumedToday = WaterEntry::where('user_id', $user->id)
            ->whereDate('entry_date', now()->format('Y-m-d'))
            ->sum('amount_ml');

        // Smart Stacks & Supplements
        $stacks = \App\Models\SmartStack::where('user_id', $user->id)
            ->with('supplements')
            ->orderBy('created_at', 'desc')
            ->get();

        $supplements = Supplement::where('user_id', $user->id)
            ->where('is_active', true)
            ->whereNull('smart_stack_id') // Independent supplements
            ->get();

        $mealLabels = [
            'breakfast' => 'Café da manhã',
            'lunch' => 'Almoço',
            'dinner' => 'Jantar',
            'snack' => 'Lanche',
            'other' => 'Outro',
        ];

        $editId = (int) $request->query('edit', 0);
        $editRow = $editId > 0 ? FoodEntry::where('id', $editId)->where('user_id', $user->id)->first() : null;

        return view('nutrition.index', [
            'tab' => $tab,
            'date' => $date,
            'stats' => $stats,
            'profile' => $profile,
            'averages' => $averages,
            'macroTargets' => $macroTargets,
            'currentGoal' => $profile->goal ?? 'maintain',
            'historyData' => $historyData,
            'consistencyCount' => $consistencyCount,
            'targetKcal' => $targetKcal,
            'waterTarget' => $waterTargetMl,
            'waterToday' => $waterConsumedToday,
            'remaining' => $remaining,
            'stacks' => $stacks,
            'supplements' => $supplements,
            'diaryRows' => $diaryRows,
            'selectedDateSums' => $selectedDateSums,
            'mealLabels' => $mealLabels,
            'editRow' => $editRow,
            'isPremium' => $isPremium,
        ]);
    }

    public function updateGoal(Request $request)
    {
        $data = $request->validate([
            'goal' => 'required|in:lose,lose_aggressive,recomp,maintain,gain,performance',
            'split' => 'required|in:cutting,bulking,maintenance',
        ]);

        $user = $request->user();
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        
        $latestWeight = \App\Models\WeightEntry::where('user_id', $user->id)->orderByDesc('weighed_at')->value('weight_kg');
        $calc = Nutrition::estimateTarget(
            (string) $profile->birth_date,
            (int) $profile->height_cm,
            $profile->sex ?? 'M',
            $profile->activity_level ?? 'moderate',
            $data['goal'],
            (float) $latestWeight
        );

        $kcal = $calc['ok'] ? $calc['target'] : ($profile->daily_calorie_target ?? 2000);

        // Apply Macro Split
        $macros = match($data['split']) {
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

        return back()->with('success', 'Estratégia nutricional atualizada com sucesso!');
    }

    public function weeklyAudit(Request $request, AIChatService $aiService)
    {
        $user = $request->user();
        $profile = UserProfile::where('user_id', $user->id)->first();
        
        $history = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(7)->format('Y-m-d'))
            ->selectRaw('entry_date, SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f, GROUP_CONCAT(food_name) as foods')
            ->groupBy('entry_date')
            ->orderBy('entry_date', 'desc')
            ->get();

        if ($history->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'Você precisa registrar pelo menos alguns dias de alimentação para uma auditoria.'
            ], 400);
        }

        $summary = $history->map(fn($d) => [
            'data' => $d->entry_date,
            'kcal' => $d->cal,
            'macros' => "P:{$d->p}g, C:{$d->c}g, F:{$d->f}g",
            'alimentos' => $d->foods
        ])->toArray();

        $prompt = "Aja como um Nutricionista Chefe de Performance. Analise meus últimos 7 dias de alimentação: " . json_encode($summary) . ". "
                . "Meu objetivo é: " . ($profile->goal ?? 'manutenção') . ". "
                . "Forneça um relatório dividido em: 1. Pontos Fortes, 2. Onde Melhorar, 3. Nota NexShape (0-100) e 4. Dica de Ouro para a próxima semana. "
                . "Seja direto, técnico e motivador.";

        $result = $aiService->chat($prompt, [
            'history_count' => count($summary),
            'objective' => $profile->goal
        ]);

        if ($result['ok']) {
            $user->consumeAiCredit('diet_audit');
            return response()->json([
                'success' => true,
                'audit' => $result['message']
            ]);
        }

        return response()->json(['success' => false, 'error' => 'Falha na auditoria.'], 500);
    }

    public function suggestMeal(Request $request, AIChatService $aiService)
    {
        $user = $request->user();
        $profile = UserProfile::where('user_id', $user->id)->first();
        
        // Calculate remaining macros (same logic as index)
        $targetKcal = $profile->daily_calorie_target ?? 2000;
        $macroTargets = Nutrition::macroTargetsForDisplay($user->hasPremiumAccess(), $profile->toArray());
        
        $todaySums = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', now()->format('Y-m-d'))
            ->selectRaw('SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f')
            ->first();

        $remaining = [
            'daily_calories' => $targetKcal,
            'consumed_calories' => $todaySums->cal ?? 0,
            'remaining_kcal' => max($targetKcal - ($todaySums->cal ?? 0), 0),
            'remaining_p' => max(($macroTargets['p'] ?? 0) - ($todaySums->p ?? 0), 0),
            'remaining_c' => max(($macroTargets['c'] ?? 0) - ($todaySums->c ?? 0), 0),
            'remaining_f' => max(($macroTargets['f'] ?? 0) - ($todaySums->f ?? 0), 0),
            'objective' => $profile->goal ?? 'maintain',
        ];

        $prompt = "Aja como um Coach de Biohacking e Especialista em Nutrição de Performance. "
                . "Baseado nos meus macros restantes para hoje ({$remaining['remaining_kcal']} kcal, {$remaining['remaining_p']}g P, {$remaining['remaining_c']}g C, {$remaining['remaining_f']}g G), "
                . "sugira UMA refeição prática e descreva brevemente a VANTAGEM FISIOLÓGICA dessa escolha (ex: controle de cortisol, otimização da síntese proteica noturna ou densidade de micronutrientes). "
                . "No FINAL da resposta, obrigatoriamente inclua uma linha com esta estrutura exata: "
                . "METRICS: [cal:X, p:Y, c:Z, f:W] "
                . "Substitua X, Y, Z, W pelos valores totais da refeição sugerida.";

        $result = $aiService->chat($prompt, $remaining);

        if ($result['ok']) {
            $user->consumeAiCredit('meal_suggestion');
            return response()->json([
                'success' => true,
                'suggestion' => $result['message'],
                'remaining' => $remaining // Pass for fallback
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Não foi possível gerar a sugestão.'
        ], 500);
    }

    public function adoptMeal(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'metrics' => 'required|array',
            'text' => 'nullable|string'
        ]);

        FoodEntry::create([
            'user_id' => $user->id,
            'entry_date' => now()->format('Y-m-d'),
            'meal_type' => 'other',
            'food_name' => 'Refeição Sugerida NexShape AI',
            'amount' => 1,
            'unit' => 'un',
            'calories' => $data['metrics']['cal'] ?? 0,
            'protein_g' => $data['metrics']['p'] ?? 0,
            'carbs_g' => $data['metrics']['c'] ?? 0,
            'fat_g' => $data['metrics']['f'] ?? 0,
        ]);

        return response()->json(['success' => true]);
    }
    public function addWater(Request $request)
    {
        $request->validate(['amount' => 'required|integer|min:1']);
        $user = $request->user();

        WaterEntry::create([
            'user_id' => $user->id,
            'entry_date' => now()->format('Y-m-d'),
            'amount_ml' => $request->amount,
            'drank_at' => now(),
            'source' => 'nutrition_hub',
        ]);

        // Invalidate dashboard cache
        \Illuminate\Support\Facades\Cache::forget("user_dashboard_stats_{$user->id}");

        return response()->json([
            'success' => true,
            'total_today' => WaterEntry::where('user_id', $user->id)
                ->whereDate('entry_date', now()->format('Y-m-d'))
                ->sum('amount_ml')
        ]);
    }

    public function repeatMeal(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'meal_type' => 'required|string',
            'source' => 'required|in:yesterday,last,specific',
            'date' => 'nullable|date',
            'target_date' => 'required|date'
        ]);

        $query = FoodEntry::where('user_id', $user->id)
            ->where('meal_type', $data['meal_type']);

        if ($data['source'] === 'yesterday') {
            $query->where('entry_date', Carbon::parse($data['target_date'])->subDay()->format('Y-m-d'));
        } elseif ($data['source'] === 'specific') {
            $query->where('entry_date', $data['date']);
        } else {
            // Last occurrence of this meal type before target_date
            $lastDate = FoodEntry::where('user_id', $user->id)
                ->where('meal_type', $data['meal_type'])
                ->where('entry_date', '<', $data['target_date'])
                ->orderByDesc('entry_date')
                ->value('entry_date');
            
            if (!$lastDate) return back()->with('error', 'Nenhuma refeição anterior encontrada.');
            $query->where('entry_date', $lastDate);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Nenhum alimento encontrado para repetir.');
        }

        foreach ($items as $item) {
            FoodEntry::create([
                'user_id' => $user->id,
                'entry_date' => $data['target_date'],
                'meal_type' => $data['meal_type'],
                'food_name' => $item->food_name,
                'amount' => $item->amount,
                'unit' => $item->unit,
                'calories' => $item->calories,
                'protein_g' => $item->protein_g,
                'carbs_g' => $item->carbs_g,
                'fat_g' => $item->fat_g,
            ]);
        }

        return back()->with('success', 'Refeição repetida com sucesso!');
    }

    public function getFavorites(Request $request)
    {
        $user = $request->user();
        $mealType = $request->query('meal_type');

        $query = FoodEntry::where('user_id', $user->id)
            ->select('food_name', 'calories', 'protein_g', 'carbs_g', 'fat_g', 'unit', DB::raw('count(*) as frequency'))
            ->groupBy('food_name', 'calories', 'protein_g', 'carbs_g', 'fat_g', 'unit')
            ->orderByDesc('frequency')
            ->limit(10);

        if ($mealType) {
            $query->where('meal_type', $mealType);
        }

        return response()->json($query->get());
    }

    public function naturalLanguageRegistry(Request $request, AIChatService $aiService)
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return response()->json(['error' => 'Recurso disponível apenas no plano Pro.'], 403);
        }

        $data = $request->validate(['text' => 'required|string|max:500']);

        $prompt = "Aja como um assistente nutricional. Analise este texto: \"{$data['text']}\". "
                . "Identifique os alimentos, estime as quantidades e forneça os macros nutricionais. "
                . "Retorne APENAS um JSON no formato: [{\"name\": \"alimento\", \"amount\": \"quantidade\", \"kcal\": 100, \"p\": 10, \"c\": 20, \"f\": 5}].";

        $result = $aiService->chat($prompt);

        if ($result['ok']) {
            // Extract JSON from response if there's any markdown
            $json = preg_replace('/^.*?(\[.*\]).*?$/s', '$1', $result['message']);
            $foods = json_decode($json, true);
            
            if ($foods) {
                return response()->json(['success' => true, 'foods' => $foods]);
            }
        }

        return response()->json(['error' => 'Não foi possível processar o registro.'], 500);
    }

    public function processPhoto(Request $request, AIChatService $aiService)
    {
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            return response()->json(['error' => 'Recurso disponível apenas no plano Pro.'], 403);
        }

        $request->validate(['photo' => 'required|image|max:5120']);

        // In a real scenario, we would send the image to a vision-capable AI.
        // For this demo, we'll simulate the AI analysis based on the requirement.
        
        $prompt = "Analise esta foto de um prato de comida (simulado). Identifique os alimentos e estime os macros. "
                . "Retorne APENAS um JSON no formato: [{\"name\": \"alimento\", \"amount\": \"quantidade\", \"kcal\": 100, \"p\": 10, \"c\": 20, \"f\": 5}].";

        $result = $aiService->chat($prompt);

        if ($result['ok']) {
            $json = preg_replace('/^.*?(\[.*\]).*?$/s', '$1', $result['message']);
            $foods = json_decode($json, true);
            if ($foods) {
                return response()->json(['success' => true, 'foods' => $foods]);
            }
        }

        return response()->json(['error' => 'Falha ao analisar a foto.'], 500);
    }

    public function manageDiary(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('nutrition.index', array_merge(['tab' => 'diary'], $request->all()));
        }

        $user = $request->user();

        // Check if deletion
        if ($request->input('action') === 'delete_food') {
            $food = FoodEntry::where('id', $request->input('food_id'))->where('user_id', $user->id)->first();
            if ($food) {
                $food->delete();
            }
            return redirect()->route('nutrition.index', ['tab' => 'diary', 'date' => $request->input('entry_date')])->with('success', 'Alimento removido com sucesso!');
        }

        // Validate generic input for adding/updating
        $data = $request->validate([
            'entry_date' => 'required|date',
            'food_name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|in:g,ml,tbsp,tsp,cup,slice,un',
            'calories' => 'required|numeric|min:0',
            'p_g' => 'nullable|numeric|min:0',
            'c_g' => 'nullable|numeric|min:0',
            'f_g' => 'nullable|numeric|min:0',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack,other',
            'food_edit_id' => 'nullable|integer',
        ]);

        // Conversion logic for household measures
        $factor = match($data['unit'] ?? 'g') {
            'tbsp' => 15,
            'tsp' => 5,
            'cup' => 240,
            'slice' => 30, // Average bread slice
            'un' => 100, // Default to 100 if unit (needs better logic per food in reality)
            default => 1,
        };

        // If unit is not g/ml, we multiply the macros by the factor and the amount
        // But usually Open Food Facts gives per 100g.
        // For simplicity, we assume the provided macros are "per 100g" if it's from search,
        // or "per unit" if manually entered.
        // Let's adjust calories and macros based on amount and unit.
        // Actually, the frontend will probably send the calculated total or the per-unit values.
        // Let's assume the frontend sends the values adjusted for 1 unit/100g, and we multiply by amount * factor / 100.
        
        $multiplier = (max(0, $data['amount'] ?? 1)) * $factor / 100;
        
        $finalKcal = max(0, round($data['calories'] * $multiplier));
        $finalP = max(0, round(($data['p_g'] ?? 0) * $multiplier, 1));
        $finalC = max(0, round(($data['c_g'] ?? 0) * $multiplier, 1));
        $finalF = max(0, round(($data['f_g'] ?? 0) * $multiplier, 1));

        if (!empty($data['food_edit_id'])) {
            $food = FoodEntry::where('id', $data['food_edit_id'])->where('user_id', $user->id)->first();
            if ($food) {
                $food->update([
                    'entry_date' => $data['entry_date'],
                    'food_name' => $data['food_name'],
                    'amount' => $data['amount'] ?? 1,
                    'unit' => $data['unit'] ?? 'un',
                    'calories' => $finalKcal,
                    'protein_g' => $finalP,
                    'carbs_g' => $finalC,
                    'fat_g' => $finalF,
                    'meal_type' => $data['meal_type'],
                ]);
            }
        } else {
            FoodEntry::create([
                'user_id' => $user->id,
                'entry_date' => $data['entry_date'],
                'food_name' => $data['food_name'],
                'amount' => $data['amount'] ?? 1,
                'unit' => $data['unit'] ?? 'un',
                'calories' => $finalKcal,
                'protein_g' => $finalP,
                'carbs_g' => $finalC,
                'fat_g' => $finalF,
                'meal_type' => $data['meal_type'],
            ]);
        }

        return redirect()->route('nutrition.index', ['tab' => 'diary', 'date' => $data['entry_date']])->with('success', 'Diário atualizado com sucesso!');
    }
}
