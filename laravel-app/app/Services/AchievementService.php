<?php

namespace App\Services;

use App\Models\UserAchievement;
use App\Models\WaterEntry;
use App\Models\ExerciseEntry;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AchievementService
{
    public function getUserBadges(User $user)
    {
        $profile = $user->profile;
        $targetWater = $profile->water_target_ml ?? 2500;
        
        // 1. Camelo de Elite (7 dias de água)
        $daysWithTargetWater = WaterEntry::where('user_id', $user->id)
            ->selectRaw('entry_date, SUM(amount_ml) as total')
            ->groupBy('entry_date')
            ->having('total', '>=', $targetWater)
            ->get()
            ->count();

        // 2. Monstro de Carga (Volume Total)
        // Cache por 1 hora para performance
        $totalVolume = Cache::remember("user_volume_{$user->id}", 3600, function() use ($user) {
            $total = 0;
            $exercises = ExerciseEntry::where('user_id', $user->id)->get();
            foreach($exercises as $exe) {
                $sets = is_array($exe->sets_data) ? $exe->sets_data : json_decode($exe->sets_data ?? '[]', true);
                if($sets) {
                    foreach($sets as $s) {
                        $total += (float)($s['weight'] ?? 0) * (float)($s['reps'] ?? 0);
                    }
                }
            }
            return $total;
        });

        // 3. Em Chamas (5 treinos na semana)
        $trainingsThisWeek = ExerciseEntry::where('user_id', $user->id)
            ->whereBetween('entry_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->distinct('entry_date')
            ->count();

        $badgeDefinitions = [
            'camelo' => [
                'title' => 'Camelo de Elite',
                'description' => "Bata a meta de água ({$targetWater}ml) por 7 dias.",
                'icon' => 'fa-solid fa-glass-water',
                'color' => 'text-blue-500',
                'bg' => 'bg-blue-500/10 border-blue-500/20',
                'meta' => 7,
                'current' => $daysWithTargetWater,
            ],
            'monstro' => [
                'title' => 'Monstro de Carga',
                'description' => "Mova um volume total de 10.000kg em seus treinos.",
                'icon' => 'fa-solid fa-dumbbell',
                'color' => 'text-emerald-500',
                'bg' => 'bg-emerald-500/10 border-emerald-500/20',
                'meta' => 10000,
                'current' => $totalVolume,
            ],
            'fogo' => [
                'title' => 'Em Chamas',
                'description' => "Treine pelo menos 5 vezes nesta semana.",
                'icon' => 'fa-solid fa-fire',
                'color' => 'text-orange-500',
                'bg' => 'bg-orange-500/10 border-orange-500/20',
                'meta' => 5,
                'current' => $trainingsThisWeek,
            ]
        ];

        $unlockedBadges = UserAchievement::where('user_id', $user->id)
            ->get()
            ->keyBy('badge_code');

        $finalBadges = [];
        $newlyUnlocked = [];
        foreach ($badgeDefinitions as $code => $data) {
            $isUnlocked = $data['current'] >= $data['meta'];
            
            // Persistir se desbloqueado agora e não estava antes
            if ($isUnlocked && !isset($unlockedBadges[$code])) {
                $achievement = UserAchievement::create([
                    'user_id' => $user->id,
                    'badge_code' => $code,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'unlocked_at' => now(),
                ]);
                $unlockedBadges[$code] = $achievement;
                $newlyUnlocked[] = $data['title'];
            }

            $finalBadges[$code] = array_merge($data, [
                'unlocked' => $isUnlocked,
                'unlocked_at' => $unlockedBadges[$code]->unlocked_at ?? null,
                'progress' => min(100, ($data['current'] / $data['meta']) * 100),
            ]);
        }

        return [
            'all' => $finalBadges,
            'new' => $newlyUnlocked
        ];
    }

    public static function getList($userId)
    {
        $user = User::find($userId);
        if (!$user) return collect([]);
        
        $service = app(self::class);
        $result = $service->getUserBadges($user);
        
        return collect($result['all'])->map(function($badge) {
            return (object) [
                'unlocked' => $badge['unlocked'],
                'icon' => $badge['icon'],
                'name' => $badge['title'],
                'color' => $badge['color'],
                'bg' => $badge['bg'],
            ];
        });
    }
}
