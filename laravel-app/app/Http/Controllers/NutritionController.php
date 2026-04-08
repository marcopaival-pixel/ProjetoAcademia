<?php

namespace App\Http\Controllers;

use App\Services\Nutrition;
use App\Models\UserProfile;
use App\Models\FoodEntry;
use App\Models\WeightEntry;
use App\Models\WaterEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class NutritionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        
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

        // Historical Data (Last 15 days)
        $historyData = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(14)->format('Y-m-d'))
            ->selectRaw('entry_date, SUM(calories) as total_cal')
            ->groupBy('entry_date')
            ->orderBy('entry_date', 'asc')
            ->get();

        // Weekly Averages & Consistency
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

        // Consistency Scoring: how many days hit target +/- 10%
        $consistencyCount = 0;
        foreach ($last7Days as $day) {
            $diff = abs($day->cal - $targetKcal);
            if ($diff <= ($targetKcal * 0.15)) { // 15% range for positive reinforcement
                $consistencyCount++;
            }
        }

        $macroTargets = Nutrition::macroTargetsForDisplay($user->hasPremiumAccess(), $profile->toArray());

        // Water
        $waterTargetMl = $profile->water_target_ml ?? 2500;
        $waterConsumedToday = WaterEntry::where('user_id', $user->id)
            ->where('entry_date', now()->format('Y-m-d'))
            ->sum('amount_ml');

        return view('nutrition.index', [
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
        ]);
    }

    public function updateGoal(Request $request)
    {
        $data = $request->validate([
            'goal' => 'required|in:lose,maintain,gain',
            'split' => 'required|in:cutting,bulking,maintenance',
        ]);

        $user = $request->user();
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        
        $latestWeight = WeightEntry::where('user_id', $user->id)->orderByDesc('weighed_at')->value('weight_kg');
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
}
