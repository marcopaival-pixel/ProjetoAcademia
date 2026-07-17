<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingModule;
use App\Models\TrainingLesson;
use App\Models\TrainingLessonCompletion;

class TrainingController extends Controller
{
    /**
     * Display a listing of the training modules.
     */
    public function index()
    {
        $user = auth()->user();
        $modules = TrainingModule::where('is_active', true)
            ->withCount(['lessons' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('order')
            ->get();

        // Calcular Progresso Global
        $allLessonsIds = TrainingLesson::where('is_active', true)->pluck('id');
        $totalLessons = $allLessonsIds->count();
        $completedLessons = $totalLessons > 0 
            ? TrainingLessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', $allLessonsIds)
                ->count()
            : 0;
            
        $globalProgress = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;

        return view('support.training.index', compact('modules', 'globalProgress', 'completedLessons', 'totalLessons'));
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
            ->with(['completions' => function($q) {
                $q->where('user_id', auth()->id());
            }])
            ->orderBy('order')
            ->get();

        $completedCount = $lessons->filter(fn($l) => $l->completions->isNotEmpty())->count();
        $totalCount = $lessons->count();
        $progress = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        return view('support.training.module', compact('module', 'lessons', 'progress', 'completedCount', 'totalCount'));
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
            ->with(['completions' => function($q) {
                $q->where('user_id', auth()->id());
            }])
            ->orderBy('order')
            ->get();

        $nextLesson = $allLessons->where('order', '>', $lesson->order)->first();
        $prevLesson = $allLessons->where('order', '<', $lesson->order)->last();
        $isCompleted = $lesson->isCompletedBy(auth()->user());

        return view('support.training.lesson', compact('module', 'lesson', 'allLessons', 'nextLesson', 'prevLesson', 'isCompleted'));
    }

    /**
     * Toggle lesson completion status.
     */
    public function toggleCompletion(TrainingLesson $lesson)
    {
        $user = auth()->user();
        $completion = TrainingLessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($completion) {
            $completion->delete();
            $message = 'Aula marcada como não concluída.';
        } else {
            TrainingLessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ]);
            $message = 'Aula concluída com sucesso!';
        }

        return back()->with('success', $message);
    }
}
