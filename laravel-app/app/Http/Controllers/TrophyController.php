<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAchievement;
use App\Models\WaterEntry;
use App\Models\ExerciseEntry;

class TrophyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        $targetWater = $profile->water_target_ml ?? 2500;
        
        // 1. Lógica Camelo (7 dias batendo meta de água)
        $daysWithTargetWater = WaterEntry::where('user_id', $user->id)
            ->selectRaw('entry_date, SUM(amount_ml) as total')
            ->groupBy('entry_date')
            ->having('total', '>=', $targetWater)
            ->get()
            ->count();

        // 2. Lógica Monstro (Carga Total)
        $totalVolume = 0;
        $exercises = ExerciseEntry::where('user_id', $user->id)->get();
        foreach($exercises as $exe) {
            $sets = is_array($exe->sets_data) ? $exe->sets_data : json_decode($exe->sets_data ?? '[]', true);
            if($sets) {
                foreach($sets as $s) {
                    $totalVolume += (float)($s['weight'] ?? 0) * (float)($s['reps'] ?? 0);
                }
            }
        }

        // 3. Lógica Fogo (5 treinos na semana atual)
        $trainingsThisWeek = ExerciseEntry::where('user_id', $user->id)
            ->whereBetween('entry_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->distinct('entry_date')
            ->count();
        
        $badges = [
            'camelo' => [
                'title' => 'Camelo de Elite',
                'description' => "Bateu a meta de água ({$targetWater}ml) em {$daysWithTargetWater}/7 dias.",
                'icon' => 'fa-solid fa-glass-water',
                'color' => 'text-blue-500',
                'bg' => 'bg-blue-500/10 border-blue-500/20',
                'unlocked' => $daysWithTargetWater >= 7,
            ],
            'monstro' => [
                'title' => 'Monstro de Carga',
                'description' => "Volume total movido: " . number_format($totalVolume, 0, ',', '.') . " kg (Meta 10.000kg).",
                'icon' => 'fa-solid fa-dumbbell',
                'color' => 'text-emerald-500',
                'bg' => 'bg-emerald-500/10 border-emerald-500/20',
                'unlocked' => $totalVolume >= 10000,
            ],
            'fogo' => [
                'title' => 'Em Chamas',
                'description' => "Treinou {$trainingsThisWeek} vezes esta semana (Meta 5).",
                'icon' => 'fa-solid fa-fire',
                'color' => 'text-orange-500',
                'bg' => 'bg-orange-500/10 border-orange-500/20',
                'unlocked' => $trainingsThisWeek >= 5,
            ]
        ];

        return view('patient.trophies', compact('badges'));
    }
}
