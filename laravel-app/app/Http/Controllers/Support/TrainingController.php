<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingModule;
use App\Models\TrainingLesson;

class TrainingController extends Controller
{
    /**
     * Display a listing of the training modules.
     */
    public function index()
    {
        $modules = TrainingModule::where('is_active', true)
            ->withCount(['lessons' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('order')
            ->get();

        return view('support.training.index', compact('modules'));
    }

    /**
     * Display the specified module and its lessons.
     */
    public function showModule(TrainingModule $module)
    {
        if (!$module->is_active) {
            abort(404);
        }

        $lessons = $module->lessons()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('support.training.module', compact('module', 'lessons'));
    }

    /**
     * Display a specific lesson and play its video.
     */
    public function showLesson(TrainingModule $module, TrainingLesson $lesson)
    {
        if (!$module->is_active || !$lesson->is_active || $lesson->module_id !== $module->id) {
            abort(404);
        }

        $allLessons = $module->lessons()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $nextLesson = $allLessons->where('order', '>', $lesson->order)->first();
        $prevLesson = $allLessons->where('order', '<', $lesson->order)->last();

        return view('support.training.lesson', compact('module', 'lesson', 'allLessons', 'nextLesson', 'prevLesson'));
    }
}
