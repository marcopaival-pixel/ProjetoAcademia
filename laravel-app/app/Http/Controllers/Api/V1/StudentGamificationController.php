<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoadLog;
use App\Models\FoodEntry;
use App\Services\AchievementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentGamificationController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request, AchievementService $achievementService): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasPremiumAccess()) {
            return $this->success([
                'is_premium_user' => false,
                'rankings' => [
                    'consistency' => [],
                    'strength' => [],
                    'nutrition' => [],
                    'elite' => [],
                ],
                'badges' => [],
            ]);
        }

        // 1. King of Consistency (Unique workout days in last 30 days)
        $consistencyRanking = LoadLog::join('users', 'load_logs.user_id', '=', 'users.id')
            ->select('users.name', 'users.id')
            ->selectRaw('count(distinct log_date) as score')
            ->where('log_date', '>=', now()->subDays(30)->format('Y-m-d'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('score')
            ->limit(10)
            ->get()
            ->map(fn($row, $index) => [
                'position' => $index + 1,
                'name' => $row->name,
                'score' => (int) $row->score,
                'is_current_user' => (int) $row->id === (int) $user->id,
            ]);

        // 2. Strength Hall of Fame (Highest Estimated 1RM)
        $strengthRanking = LoadLog::join('users', 'load_logs.user_id', '=', 'users.id')
            ->join('exercises_catalog', 'load_logs.exercise_id', '=', 'exercises_catalog.id')
            ->select('users.name', 'users.id', 'exercises_catalog.name as exercise_name')
            ->selectRaw('MAX(weight_kg / (1.0278 - 0.0278 * reps_done)) as score')
            ->where('reps_done', '>', 0)
            ->where('reps_done', '<=', 12)
            ->groupBy('users.id', 'users.name', 'exercises_catalog.id', 'exercises_catalog.name')
            ->orderByDesc('score')
            ->limit(10)
            ->get()
            ->map(fn($row, $index) => [
                'position' => $index + 1,
                'name' => $row->name . " ({$row->exercise_name})",
                'score' => (int) round($row->score),
                'is_current_user' => (int) $row->id === (int) $user->id,
            ]);

        // 3. Nutrition Masters (Daily consistency - food entries last 7 days)
        $nutritionRanking = FoodEntry::join('users', 'food_entries.user_id', '=', 'users.id')
            ->select('users.name', 'users.id')
            ->selectRaw('count(*) as score')
            ->where('entry_date', '>=', now()->subDays(7)->format('Y-m-d'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('score')
            ->limit(10)
            ->get()
            ->map(fn($row, $index) => [
                'position' => $index + 1,
                'name' => $row->name,
                'score' => (int) $row->score,
                'is_current_user' => (int) $row->id === (int) $user->id,
            ]);

        // 4. Elite All-Around (Weighted Score)
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
            : User::whereIn('id', $candidateIds)->get()->map(function ($u) {
                $consistencyCount = LoadLog::where('user_id', $u->id)
                    ->where('log_date', '>=', now()->subDays(30))
                    ->distinct()
                    ->count('log_date');

                $maxStrengthValue = (float) (LoadLog::where('user_id', $u->id)
                    ->selectRaw('MAX(weight_kg / (1.0278 - 0.0278 * reps_done)) as one_rm')
                    ->where('reps_done', '>', 0)
                    ->value('one_rm') ?? 0);

                $nutritionCount = FoodEntry::where('user_id', $u->id)
                    ->where('entry_date', '>=', now()->subDays(7))
                    ->count();

                $score = ($consistencyCount * 50) + ($maxStrengthValue * 2) + ($nutritionCount * 10);

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'score' => (int) round($score),
                ];
            })
            ->sortByDesc('score')
            ->values()
            ->take(10)
            ->map(fn($row, $index) => [
                'position' => $index + 1,
                'name' => $row['name'],
                'score' => $row['score'],
                'is_current_user' => (int) $row['id'] === (int) $user->id,
            ]);

        // 5. User Badges / Troféus
        $badgesResult = $achievementService->getUserBadges($user);
        $badges = collect($badgesResult['all'] ?? [])->map(fn($badge, $code) => [
            'code' => $code,
            'title' => $badge['title'] ?? 'Troféu',
            'description' => $badge['description'] ?? '',
            'meta' => (int) ($badge['meta'] ?? 0),
            'current' => (int) ($badge['current'] ?? 0),
            'is_unlocked' => (bool) ($badge['unlocked'] ?? false),
            'color' => $badge['color'] ?? 'text-gray-500',
        ])->values()->all();

        return $this->success([
            'is_premium_user' => true,
            'rankings' => [
                'consistency' => $consistencyRanking,
                'strength' => $strengthRanking,
                'nutrition' => $nutritionRanking,
                'elite' => $eliteRanking,
            ],
            'badges' => $badges,
        ]);
    }
}
