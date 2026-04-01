<?php

namespace App\Http\Controllers;

use App\Services\Nutrition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $uid = (int) $user->id;
        $isPremium = $user->isPremiumActive();
        $today = now()->format('Y-m-d');

        if ($request->isMethod('post') && $request->has('water_add')) {
            $request->validate(['water_add' => ['required', 'integer', 'min:1']]);
            DB::table('water_entries')->insert([
                'user_id' => $uid,
                'entry_date' => $today,
                'amount_ml' => (int) $request->input('water_add'),
            ]);

            return redirect()->route('dashboard');
        }

        $prof = DB::table('user_profiles')->where('user_id', $uid)->first();
        $profArr = $prof ? (array) $prof : [];
        $calorieTarget = isset($prof?->daily_calorie_target) && $prof->daily_calorie_target !== null
            ? (int) $prof->daily_calorie_target : null;
        $waterTarget = isset($prof?->water_target_ml) && $prof->water_target_ml !== null
            ? (int) $prof->water_target_ml : 2000;
        $macroTargets = Nutrition::macroTargetsForDisplay($isPremium, $profArr);
        $hasMacroTargets = $isPremium
            ? (($macroTargets['p'] ?? 0) > 0 || ($macroTargets['c'] ?? 0) > 0 || ($macroTargets['f'] ?? 0) > 0)
            : ($calorieTarget !== null);

        $foodSums = DB::table('food_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $today)
            ->selectRaw('COALESCE(SUM(calories), 0) as c, COALESCE(SUM(protein_g), 0) as p, COALESCE(SUM(carbs_g), 0) as cb, COALESCE(SUM(fat_g), 0) as f')
            ->first();
        $consumed = (int) ($foodSums->c ?? 0);
        $sumProt = (float) ($foodSums->p ?? 0);
        $sumCarb = (float) ($foodSums->cb ?? 0);
        $sumFat = (float) ($foodSums->f ?? 0);

        $burned = (int) DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $today)
            ->whereNotNull('calories_burned')
            ->sum('calories_burned');

        $remaining = $calorieTarget !== null ? $calorieTarget - $consumed + $burned : null;

        $lastWeight = DB::table('weight_entries')
            ->where('user_id', $uid)
            ->orderByDesc('weighed_at')
            ->first();

        $waterConsumed = (int) DB::table('water_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $today)
            ->sum('amount_ml');

        return view('dashboard', compact(
            'today',
            'calorieTarget',
            'waterTarget',
            'macroTargets',
            'hasMacroTargets',
            'consumed',
            'sumProt',
            'sumCarb',
            'sumFat',
            'burned',
            'remaining',
            'lastWeight',
            'waterConsumed',
            'isPremium',
        ));
    }
}
