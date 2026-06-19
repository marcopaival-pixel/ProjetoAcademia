<?php

namespace App\Http\Controllers;

use App\Models\TrainingPlan;
use App\Models\ExerciseCatalog;
use App\Models\TrainingPlanExercise;
use App\Models\Muscle;
use App\Models\MuscleGroup;
use Illuminate\Http\Request;
use App\Support\PatientAccessGuard;
use Illuminate\Support\Facades\Auth;
use App\Services\DompdfPdfService;

class TrainingPlanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isPremium = $user->hasPremiumAccess();
        
        $targetUserId = $user->id;
        if ($user->isProfessional()) {
            $activePatientId = PatientAccessGuard::resolveActivePatientId($user);
            if ($activePatientId) {
                $targetUserId = $activePatientId;
            }
        }

        $query = TrainingPlan::where('user_id', $targetUserId)
            ->withCount('exercises')
            ->latest();

        if (!$isPremium) {
            $plans = $query->limit(3)->get();
        } else {
            $plans = $query->get();
        }
            
        return view('progression.plans-index', compact('plans', 'isPremium'));
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

        $musclesByGroup = MuscleGroup::with('muscles')->get();

        return view('progression.body-target', compact('sex', 'musclesByGroup'));
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
        $user = Auth::user();
        if (!$user->hasFeature('create_workout')) {
             return redirect()->route('progression.plans.index')
                 ->with('error', 'Seu plano atual não permite a criação de planilhas de treino.');
        }

        $targetUserId = $user->id;
        if ($user->isProfessional()) {
            $activePatientId = PatientAccessGuard::resolveActivePatientId($user);
            if ($activePatientId) {
                $targetUserId = $activePatientId;
            }
        }

        $targetUser = \App\Models\User::findOrFail($targetUserId);
        $maxWorkouts = $targetUser->getPlanLimit('max_workouts');

        if ($maxWorkouts > 0) {
            $planCount = TrainingPlan::where('user_id', $targetUserId)->count();
            if ($planCount >= $maxWorkouts) {
                return redirect()->route('progression.plans.index')
                    ->with('error', "O limite de {$maxWorkouts} planos de treino do plano foi atingido. Faça upgrade para Pro para criar rotinas ilimitadas!");
            }
        }

        $selectedTargets = session('selected_body_targets', []);
        $catalog = ExerciseCatalog::where('is_active', true)->with('muscles')->get()->groupBy('muscle_group');
        return view('progression.plans-create', compact('catalog', 'selectedTargets'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasFeature('create_workout')) {
             return redirect()->route('progression.plans.index')
                ->with('error', 'Seu plano atual não permite a criação de planilhas de treino.');
        }

        // Se vier via JSON (Alpine), decodifica para validar como array
        if ($request->filled('exercises_json')) {
            $exercises = json_decode($request->exercises_json, true);
            if (is_array($exercises)) {
                $request->merge(['exercises' => $exercises]);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'plan_label' => 'nullable|string|max:10',
            'goal' => 'nullable|string|max:50',
            'goal_custom' => 'nullable|string|max:100',
            'frequency' => 'nullable|integer|min:1|max:7',
            'difficulty' => 'nullable|string|max:20',
            'estimated_duration' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'student_profile' => 'nullable|string|max:30',
            'split_type' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:20',
            'days_of_week' => 'nullable|string', // JSON string from hidden input
            'is_template' => 'nullable|boolean',
            'total_volume' => 'nullable|numeric',
            'muscles_worked' => 'nullable|string', // JSON string from hidden input
            'exercises' => [
                'required', 
                'array', 
                'min:1',
                function ($attribute, $value, $fail) use ($user) {
                    $limit = $user->getPlanLimit('max_exercises_per_workout'); // I need to add this to PlanSeeder too
                    if ($limit > 0 && count($value) > $limit) {
                        $fail("O limite de exercícios por treino no seu plano é de {$limit}.");
                    }
                }
            ],
            'exercises.*.id' => 'required|exists:exercises_catalog,id',
            'exercises.*.sets' => 'required|array|min:1',
        ], [
            'name.required' => 'O nome do plano de treino é obrigatório.',
            'exercises.required' => 'Seu plano precisa ter pelo menos um exercício.',
            'exercises.min' => 'Seu plano precisa ter pelo menos um exercício.',
            'exercises.*.sets.required' => 'Cada exercício precisa ter pelo menos uma série definida.',
            'exercises.*.sets.min' => 'Cada exercício precisa ter pelo menos uma série definida.',
        ]);

        $goal = $validated['goal'] === 'Outro objetivo' ? $validated['goal_custom'] : $validated['goal'];

        $targetUserId = Auth::id();
        if ($user->isProfessional()) {
            $activePatientId = PatientAccessGuard::resolveActivePatientId($user);
            if (! $activePatientId) {
                return back()->with('error', 'Selecione um paciente para registrar o plano de treino.');
            }
            $targetUserId = $activePatientId;
        }

        $plan = TrainingPlan::create([
            'user_id' => $targetUserId,
            'creator_id' => Auth::id(),
            'name' => $validated['name'],
            'plan_label' => $request->plan_label,
            'goal' => $goal,
            'frequency' => $request->frequency,
            'difficulty' => $request->difficulty,
            'student_profile' => $validated['student_profile'],
            'split_type' => $validated['split_type'],
            'status' => $validated['status'] ?? 'Rascunho',
            'days_of_week' => json_decode($request->days_of_week, true),
            'is_template' => ($request->is_template && $user->hasFeature('create_workout_model')) ? true : false,
            'estimated_duration' => $request->estimated_duration,
            'total_volume' => $request->total_volume ?? 0,
            'muscles_worked' => json_decode($request->muscles_worked, true),
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
                    'rpe_target' => $setData['rpe'] ?? null,
                    'cadence' => $setData['cadence'] ?? null,
                    'set_type' => $setData['type'] ?? 'work',
                ]);
            }
        }

        $selectedTargets = session('selected_body_targets', []);
        $photoPath = session('body_target_photo', null);

        if (!empty($selectedTargets)) {
            foreach ($selectedTargets as $target) {
                $targetName = is_array($target) ? $target['name'] : $target;
                $muscleId = is_array($target) ? ($target['id'] ?? null) : null;

                \App\Models\WorkoutTargetArea::create([
                    'user_id' => $targetUserId,
                    'training_plan_id' => $plan->id,
                    'target_area' => $targetName,
                    'muscle_id' => $muscleId,
                    'reference_photo_path' => $photoPath,
                ]);
            }
            session()->forget(['selected_body_targets', 'body_target_photo']);
        }

        return redirect()->route('progression.plans.index')->with('success', 'Plano de treino criado com sucesso!');
    }

    public function show(TrainingPlan $plan)
    {
        $this->authorize('view', $plan);
        
        if (Auth::user()->isResourceOverLimit('workouts', $plan->id)) {
            return redirect()->route('progression.plans.index')->with('error', 'Este plano de treino está bloqueado por exceder o limite do seu plano atual. Faça upgrade para acessá-lo.');
        }

        $plan->load('exercises.catalogExercise', 'exercises.sets');
        return view('progression.plans-show', compact('plan'));
    }

    public function edit(TrainingPlan $plan)
    {
        $this->authorize('update', $plan);

        if (Auth::user()->isResourceOverLimit('workouts', $plan->id)) {
            return redirect()->route('progression.plans.index')->with('error', 'Este plano de treino está bloqueado por exceder o limite do seu plano atual. Faça upgrade para editá-lo.');
        }
        
        $plan->load('exercises.catalogExercise', 'exercises.sets');
        $catalog = ExerciseCatalog::where('is_active', true)->with('muscles')->get()->groupBy('muscle_group');
        return view('progression.plans-edit', compact('plan', 'catalog'));
    }

    public function update(Request $request, TrainingPlan $plan)
    {
        $this->authorize('update', $plan);
        $user = Auth::user();

        if ($user->isResourceOverLimit('workouts', $plan->id)) {
            return redirect()->route('progression.plans.index')->with('error', 'Este plano de treino está bloqueado por exceder o limite do seu plano atual. Faça upgrade para editá-lo.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'plan_label' => 'nullable|string|max:10',
            'goal' => 'nullable|string|max:50',
            'goal_custom' => 'nullable|string|max:100',
            'frequency' => 'nullable|integer|min:1|max:7',
            'difficulty' => 'nullable|string|max:20',
            'estimated_duration' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'student_profile' => 'nullable|string|max:30',
            'split_type' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:20',
            'days_of_week' => 'nullable|string',
            'is_template' => 'nullable|boolean',
            'total_volume' => 'nullable|numeric',
            'muscles_worked' => 'nullable|string',
            'exercises' => 'required|array|min:1',
            'exercises.*.id' => 'required|exists:exercises_catalog,id',
            'exercises.*.sets' => 'required|array|min:1',
        ]);

        $goal = $validated['goal'] === 'Outro objetivo' ? $validated['goal_custom'] : $validated['goal'];

        $plan->update([
            'name' => $validated['name'],
            'plan_label' => $request->plan_label,
            'goal' => $goal,
            'frequency' => $request->frequency,
            'difficulty' => $request->difficulty,
            'student_profile' => $validated['student_profile'],
            'split_type' => $validated['split_type'],
            'status' => $validated['status'] ?? 'Rascunho',
            'days_of_week' => json_decode($request->days_of_week, true),
            'is_template' => ($request->is_template && $user->hasFeature('create_workout_model')) ? true : false,
            'estimated_duration' => $request->estimated_duration,
            'total_volume' => $request->total_volume ?? 0,
            'muscles_worked' => json_decode($request->muscles_worked, true),
            'description' => $validated['description'],
        ]);

        $plan->exercises()->delete(); 

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
                    'rpe_target' => $setData['rpe'] ?? null,
                    'cadence' => $setData['cadence'] ?? null,
                    'set_type' => $setData['type'] ?? 'work',
                ]);
            }
        }

        return redirect()->route('progression.plans.index')->with('success', 'Plano de treino atualizado com sucesso!');
    }

    public function duplicate(TrainingPlan $plan)
    {
        $this->authorize('view', $plan);

        if (Auth::user()->isResourceOverLimit('workouts', $plan->id)) {
            return redirect()->route('progression.plans.index')->with('error', 'Este plano de treino está bloqueado por exceder o limite do seu plano atual. Faça upgrade para duplicá-lo.');
        }

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

    public function destroy(TrainingPlan $plan)
    {
        $this->authorize('delete', $plan);
        
        $plan->delete();
        return redirect()->route('progression.plans.index')->with('success', 'Plano de treino excluído!');
    }

    public function exportPdf(TrainingPlan $plan, DompdfPdfService $dompdfPdf)
    {
        $this->authorize('view', $plan);

        $plan->load('exercises.catalogExercise', 'exercises.sets');
        $user = Auth::user();

        $html = view('progression.pdf-report', compact('plan', 'user'))->render();

        $binary = $dompdfPdf->render($html, 'A4', 'portrait', true, 'DejaVu Sans');

        return response($binary, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Treino_'.$plan->name.'.pdf"');
    }

    public function searchMuscles(Request $request)
    {
        $query = $request->get('q');
        $muscles = Muscle::with('group')
            ->where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($muscle) {
                return [
                    'id' => $muscle->id,
                    'name' => $muscle->name,
                    'group' => $muscle->group->name,
                    'type' => $muscle->type,
                ];
            });

        return response()->json($muscles);
    }
}
