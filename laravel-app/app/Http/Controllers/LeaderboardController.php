<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoadLog;
use App\Models\FoodEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        // 1. King of Consistency (Unique workout days in last 30 days)
        $consistencyRanking = LoadLog::join('users', 'load_logs.user_id', '=', 'users.id')
            ->select('users.name', 'users.id')
            ->selectRaw('count(distinct log_date) as workout_days')
            ->where('log_date', '>=', now()->subDays(30)->format('Y-m-d'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('workout_days')
            ->limit(10)
            ->get();

        // 2. Strength Hall of Fame (Highest Estimated 1RM)
        // Formula: Weight / (1.0278 - 0.0278 * Reps)
        $strengthRanking = LoadLog::join('users', 'load_logs.user_id', '=', 'users.id')
            ->join('exercises_catalog', 'load_logs.exercise_id', '=', 'exercises_catalog.id')
            ->select('users.name as user_name', 'exercises_catalog.name as exercise_name')
            ->selectRaw('MAX(weight_kg / (1.0278 - 0.0278 * reps_done)) as max_one_rm')
            ->where('reps_done', '>', 0)
            ->where('reps_done', '<=', 12) // Brzycki formula is more accurate in this range
            ->groupBy('users.id', 'users.name', 'exercises_catalog.id', 'exercises_catalog.name')
            ->orderByDesc('max_one_rm')
            ->limit(10)
            ->get();

        // 3. Nutrition Masters (Daily consistency - users with most food entries logged)
        $nutritionRanking = FoodEntry::join('users', 'food_entries.user_id', '=', 'users.id')
            ->select('users.name')
            ->selectRaw('count(*) as logs_count')
            ->where('entry_date', '>=', now()->subDays(7)->format('Y-m-d'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('logs_count')
            ->limit(10)
            ->get();

        return view('leaderboard.index', compact('consistencyRanking', 'strengthRanking', 'nutritionRanking'));
    }
}
