<?php

namespace App\Observers;

use App\Models\WaterEntry;
use App\Services\AchievementService;

class WaterEntryObserver
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function created(WaterEntry $waterEntry)
    {
        $result = $this->achievementService->getUserBadges($waterEntry->user);
        
        if (!empty($result['new'])) {
            foreach ($result['new'] as $title) {
                session()->flash('success', "🏆 Conquista Desbloqueada: {$title}!");
            }
        }
    }
}
