<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingModule;
use App\Models\TrainingLesson;
use Illuminate\Support\Str;

class TrainingController extends Controller
{
    /**
     * Display a listing of modules and lessons.
     */
    public function index()
    {
        $modules = TrainingModule::with('lessons')->orderBy('order')->get();
        return view('admin.support.training.index', compact('modules'));
    }

    /**
     * Store a newly created module.
     */
    public function storeModule(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        TrainingModule::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'order' => TrainingModule::max('order') + 1,
        ]);

        return back()->with('success', 'Módulo criado com sucesso!');
    }

    /**
     * Store a newly created lesson.
     */
    public function storeLesson(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:training_modules,id',
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
        ]);

        TrainingLesson::create([
            'module_id' => $request->module_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'video_url' => $request->video_url,
            'content' => $request->content,
            'order' => TrainingLesson::where('module_id', $request->module_id)->max('order') + 1,
        ]);

        return back()->with('success', 'Aula criada com sucesso!');
    }

    /**
     * Delete a module.
     */
    public function destroyModule(TrainingModule $module)
    {
        $module->delete();
        return back()->with('success', 'Módulo e suas aulas foram removidos!');
    }

    /**
     * Delete a lesson.
     */
    public function destroyLesson(TrainingLesson $lesson)
    {
        $lesson->delete();
        return back()->with('success', 'Aula removida!');
    }
}
