<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoadLog;
use App\Models\FoodEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // 4. Elite All-Around Ranking (Weighted Score)
        // Nunca usar User::all() — com muitos registos esgota a RAM (512MB+). Só candidatos com atividade recente.
        $candidateIds = DB::table('load_logs')
            ->select('user_id')
            ->where('log_date', '>=', now()->subDays(30)->toDateString())
            ->groupBy('user_id')
            ->limit(400)
            ->pluck('user_id')
            ->merge(
                DB::table('food_entries')
                    ->select('user_id')
                    ->where('entry_date', '>=', now()->subDays(7)->toDateString())
                    ->groupBy('user_id')
                    ->limit(400)
                    ->pluck('user_id')
            )
            ->unique()
            ->values()
            ->take(400);

        $eliteRanking = $candidateIds->isEmpty()
            ? collect()
            : User::whereIn('id', $candidateIds)->get()->map(function ($user) use ($consistencyRanking, $strengthRanking, $nutritionRanking) {
                $consistencyCount = LoadLog::where('user_id', $user->id)
                    ->where('log_date', '>=', now()->subDays(30))
                    ->distinct()
                    ->count('log_date');

                $maxStrengthValue = (float) (LoadLog::where('user_id', $user->id)
                    ->selectRaw('MAX(weight_kg / (1.0278 - 0.0278 * reps_done)) as one_rm')
                    ->where('reps_done', '>', 0)
                    ->value('one_rm') ?? 0);

                $nutritionCount = FoodEntry::where('user_id', $user->id)
                    ->where('entry_date', '>=', now()->subDays(7))
                    ->count();

                $score = ($consistencyCount * 50) + ($maxStrengthValue * 2) + ($nutritionCount * 10);

                // Categoria e Nível
                $category = 'Iniciante';
                $level = 'Bronze';
                
                if ($score > 1500) {
                    $category = 'Elite';
                    $level = 'Diamond';
                } elseif ($score > 1000) {
                    $category = 'Elite';
                    $level = 'Platinum';
                } elseif ($score > 600) {
                    $category = 'Intermediário';
                    $level = 'Gold';
                } elseif ($score > 300) {
                    $category = 'Intermediário';
                    $level = 'Silver';
                }

                // Medalhas Dinâmicas
                $medals = [];
                if ($consistencyRanking->where('id', $user->id)->first()) $medals[] = ['type' => 'constancy', 'icon' => 'clock', 'color' => 'blue', 'label' => 'Metrônomo'];
                if ($strengthRanking->where('user_name', $user->name)->first()) $medals[] = ['type' => 'strength', 'icon' => 'dumbbell', 'color' => 'amber', 'label' => 'Titã'];
                if ($nutritionRanking->where('name', $user->name)->first()) $medals[] = ['type' => 'nutrition', 'icon' => 'leaf', 'color' => 'emerald', 'label' => 'Imunidade'];

                return (object) [
                    'name' => $user->name,
                    'score' => round($score),
                    'level' => $level,
                    'category' => $category,
                    'medals' => $medals,
                    'is_premium' => $user->hasPremiumAccess(),
                ];
            })->sortByDesc('score')->values()->take(10);

        return view('leaderboard.index', compact('consistencyRanking', 'strengthRanking', 'nutritionRanking', 'eliteRanking'));
    }
}
