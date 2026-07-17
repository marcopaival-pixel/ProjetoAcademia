<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAchievement;
use App\Models\WaterEntry;
use App\Models\ExerciseEntry;

class TrophyController extends Controller
{
    protected $achievementService;

    public function __construct(\App\Services\AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function index(Request $request)
    {
        $result = $this->achievementService->getUserBadges($request->user());
        $badges = $result['all'];
        return view('student.trophies', compact('badges'));
    }
}
