<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class AdminOverviewStats
{
    /**
     * Métricas agregadas para a área administrativa (somente leitura).
     *
     * @return array{
     *     total_users: int,
     *     administrators: int,
     *     premium_subscriptions_active: int,
     *     new_users_7d: int,
     *     distinct_food_loggers_7d: int,
     * }
     */
    public static function collect(): array
    {
        $now = CarbonImmutable::now();
        $since7Start = $now->subDays(7)->startOfDay();

        $distinctFoodLoggers = (int) DB::table('food_entries')
            ->where('entry_date', '>=', $since7Start->toDateString())
            ->selectRaw('COUNT(DISTINCT user_id) as aggregate')
            ->value('aggregate');

        $since30Start = $now->subDays(30)->startOfDay();

        $activeUsers = (int) DB::table('food_entries')
            ->where('entry_date', '>=', $since30Start->toDateString())
            ->union(
                DB::table('exercise_entries')
                    ->where('entry_date', '>=', $since30Start->toDateString())
                    ->select('user_id')
            )
            ->select('user_id')
            ->distinct()
            ->count();

        return [
            'total_users' => User::query()->count(),
            'administrators' => User::query()->where('is_admin', true)->count(),
            'premium_subscriptions_active' => User::query()
                ->where('is_premium', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('premium_expires_at')
                        ->orWhere('premium_expires_at', '>=', $now);
                })
                ->count(),
            'new_users_7d' => User::query()->where('created_at', '>=', $since7Start)->count(),
            'distinct_food_loggers_7d' => $distinctFoodLoggers,
            'active_users_30d' => $activeUsers,
        ];
    }
}
