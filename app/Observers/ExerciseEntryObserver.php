<?php

namespace App\Observers;

use App\Models\ExerciseEntry;
use App\Services\AchievementService;
use Illuminate\Support\Facades\Cache;

class ExerciseEntryObserver
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function created(ExerciseEntry $exerciseEntry)
    {
        // Limpar cache de volume do usuário
        Cache::forget("user_volume_{$exerciseEntry->user_id}");
        
        // Verificar conquistas
        $result = $this->achievementService->getUserBadges($exerciseEntry->user);
        
        if (!empty($result['new'])) {
            foreach ($result['new'] as $title) {
                session()->flash('success', "🏆 Conquista Desbloqueada: {$title}!");
            }
        }
    }
}
