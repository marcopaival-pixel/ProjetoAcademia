<?php

namespace App\Http\Controllers;

use App\Models\TrainingPlan;
use App\Models\ExerciseCatalog;
use App\Models\TrainingPlanExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;

class TrainingPlanController extends Controller
{
    public function index()
    {
        $plans = TrainingPlan::where('user_id', Auth::id())
            ->withCount('exercises')
            ->get();
            
        return view('progression.plans-index', compact('plans'));
    }

    public function targetSelection()
    {
        $user = Auth::user();
        $user->load('profile');
        $sex = $user->profile?->sex ?? 'M';

        // Auto-copiar imagens geradas para public/images/body/ se ainda não existirem
        $src = 'C:/Users/paiva/.gemini/antigravity/brain/5d69c1a7-3818-40c0-9e37-4d2d45b59bf0/';
        $dst = public_path('images/body/');
        $map = [
            'body_female_front_1775673375488.png' => 'female_front.png',
            'body_female_back_1775673389987.png'  => 'female_back.png',
            'body_male_front_1775673405350.png'   => 'male_front.png',
            'body_male_back_1775673421690.png'    => 'male_back.png',
        ];
        if (!is_dir($dst)) {
            @mkdir($dst, 0755, true);
        }
        foreach ($map as $from => $to) {
            if (!file_exists($dst . $to) && file_exists($src . $from)) {
                @copy($src . $from, $dst . $to);
            }
        }

        return view('progression.body-target', compact('sex'));
    }

    public function storeTargetSelection(Request $request)
    {
        $request->validate([
            'targets' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $targets = json_decode($request->targets, true);
        if (!is_array($targets) || count($targets) === 0) {
             return back()->withErrors(['targets' => 'Selecione ou digite pelo menos uma área corporal.']);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('target_areas', 'public');
        }

        session([
            'selected_body_targets' => $targets,
            'body_target_photo' => $photoPath
        ]);

        return redirect()->route('progression.plans.create');
    }

    public function create()
    {
        $selectedTargets = session('selected_body_targets', []);
        $catalog = ExerciseCatalog::where('is_active', true)->get()->groupBy('muscle_group');
        return view('progression.plans-create', compact('catalog', 'selectedTargets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'plan_label' => 'nullable|string|max:10',
            'goal' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'exercises' => 'required|array',
            'exercises.*.id' => 'required|exists:exercises_catalog,id',
            'exercises.*.sets' => 'required|array',
        ]);

        $plan = TrainingPlan::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'plan_label' => $request->plan_label,
            'goal' => $request->goal,
            'description' => $validated['description'],
        ]);

        foreach ($validated['exercises'] as $index => $exData) {
            $tpExercise = TrainingPlanExercise::create([
                'training_plan_id' => $plan->id,
                'exercise_id' => $exData['id'],
                'position' => $index,
            ]);

            foreach ($exData['sets'] as $setIndex => $setData) {
                $tpExercise->sets()->create([
                    'set_number' => $setIndex + 1,
                    'reps_target' => $setData['reps'] ?? 0,
                    'weight_target' => $setData['weight'] ?? 0,
                    'rest_seconds' => $setData['rest'] ?? 60,
                ]);
            }
        }

        $selectedTargets = session('selected_body_targets', []);
        $photoPath = session('body_target_photo', null);

        if (!empty($selectedTargets)) {
            foreach ($selectedTargets as $target) {
                \App\Models\WorkoutTargetArea::create([
                    'user_id' => Auth::id(),
                    'training_plan_id' => $plan->id,
                    'target_area' => $target,
                    'reference_photo_path' => $photoPath,
                ]);
            }
            session()->forget(['selected_body_targets', 'body_target_photo']);
        }

        return redirect()->route('progression.plans.index')->with('success', 'Plano de treino criado com sucesso!');
    }

    public function show(TrainingPlan $plan)
    {
        if ($plan->user_id !== Auth::id()) abort(403);
        
        $plan->load('exercises.catalogExercise', 'exercises.sets');
        return view('progression.plans-show', compact('plan'));
    }

    public function duplicate(TrainingPlan $plan)
    {
        if ($plan->user_id !== Auth::id()) abort(403);

        $newPlan = $plan->replicate();
        $newPlan->name = $plan->name . ' (Cópia)';
        $newPlan->save();

        foreach ($plan->exercises as $exercise) {
            $newEx = $exercise->replicate();
            $newEx->training_plan_id = $newPlan->id;
            $newEx->save();

            foreach ($exercise->sets as $set) {
                $newSet = $set->replicate();
                $newSet->training_plan_exercise_id = $newEx->id;
                $newSet->save();
            }
        }

        return redirect()->route('progression.plans.index')->with('success', 'Treino duplicado com sucesso!');
    }

    public function exportPdf(TrainingPlan $plan)
    {
        if ($plan->user_id !== Auth::id()) abort(403);

        $plan->load('exercises.catalogExercise', 'exercises.sets');
        $user = Auth::user();

        $html = view('progression.pdf-report', compact('plan', 'user'))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Treino_'.$plan->name.'.pdf"');
    }
}
