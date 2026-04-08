<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\LoadLog;

class AchievementService
{
    private static $badges = [
        'pioneer' => [
            'name' => 'Pioneiro',
            'desc' => 'Completou o primeiro treino no sistema.',
            'icon' => '🚀'
        ],
        'iron_heart' => [
            'name' => 'Coração de Ferro',
            'desc' => 'Completou 10 sessões de treino.',
            'icon' => '❤️'
        ],
        'beast_mode' => [
            'name' => 'Beast Mode',
            'desc' => 'Ergueu 100kg em qualquer exercício.',
            'icon' => '💪'
        ],
        'consistency_king' => [
            'name' => 'Rei da Constância',
            'desc' => 'Treinou 5 dias seguidos.',
            'icon' => '👑'
        ],
        'data_master' => [
            'name' => 'Mestre dos Dados',
            'desc' => 'Aceitou os termos LGPD e configurou o perfil.',
            'icon' => '🛡️'
        ]
    ];

    public static function check(int $userId)
    {
        $newBadges = [];

        // 1. Pioneer (any log)
        if (!self::has($userId, 'pioneer')) {
            if (DB::table('load_logs')->where('user_id', $userId)->exists()) {
                self::award($userId, 'pioneer');
            }
        }

        // 2. Iron Heart (10 logs)
        if (!self::has($userId, 'iron_heart')) {
            $count = DB::table('load_logs')
                ->where('user_id', $userId)
                ->select(DB::raw('count(distinct log_date) as c'))
                ->first()->c;
            if ($count >= 10) self::award($userId, 'iron_heart');
        }

        // 3. Beast Mode (100kg in any set)
        if (!self::has($userId, 'beast_mode')) {
            if (DB::table('load_logs')->where('user_id', $userId)->where('weight_kg', '>=', 100)->exists()) {
                self::award($userId, 'beast_mode');
            }
        }
    }

    private static function has($userId, $slug)
    {
        return DB::table('achievements')->where('user_id', $userId)->where('badge_slug', $slug)->exists();
    }

    private static function award($userId, $slug)
    {
        DB::table('achievements')->insert(['user_id' => $userId, 'badge_slug' => $slug, 'achieved_at' => now()]);
    }

    public static function getList($userId)
    {
        $unlocked = DB::table('achievements')->where('user_id', $userId)->pluck('badge_slug')->toArray();
        
        $result = [];
        foreach (self::$badges as $slug => $data) {
            $data['unlocked'] = in_array($slug, $unlocked);
            $result[] = (object) $data;
        }
        return $result;
    }
}
