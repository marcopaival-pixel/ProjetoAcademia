<?php

namespace App\Http\Controllers;

use App\Models\TrainingPlan;
use App\Models\LoadLog;
use App\Models\TrainingPlanExercise;
use App\Services\ProgressionService;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoadProgressionController extends Controller
{
    public function logSession(TrainingPlan $plan)
    {
        if ($plan->user_id !== Auth::id()) abort(403);
        
        $plan->load('exercises.catalogExercise', 'exercises.sets');
        
        // Buscar logs anteriores e calcular sugestão de carga
        foreach ($plan->exercises as $exercise) {
            $lastLog = LoadLog::where('user_id', Auth::id())
                ->where('exercise_id', $exercise->exercise_id)
                ->orderBy('log_date', 'desc')
                ->first();

            $exercise->last_log = $lastLog;
            
            // Usar o primeiro set como referência de repetições alvo
            $targetReps = $exercise->sets->first()->reps_target ?? 10;
            
            $exercise->suggestion = ProgressionService::suggestLoad(
                Auth::id(),
                $exercise->exercise_id,
                $lastLog->weight_kg ?? ($exercise->sets->first()->weight_target ?? 0),
                $targetReps
            );
        }

        return view('progression.session-log', compact('plan'));
    }

    public function storeLog(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'logs' => 'required|array',
            'logs.*.training_plan_exercise_id' => 'required|exists:training_plan_exercises,id',
            'logs.*.exercise_id' => 'required|exists:exercises_catalog,id',
            'logs.*.sets' => 'required|array',
        ]);

        foreach ($validated['logs'] as $exLog) {
            foreach ($exLog['sets'] as $setIndex => $setData) {
                if (empty($setData['weight']) || empty($setData['reps'])) continue;

                LoadLog::create([
                    'user_id' => Auth::id(),
                    'training_plan_exercise_id' => $exLog['training_plan_exercise_id'],
                    'exercise_id' => $exLog['exercise_id'],
                    'log_date' => $validated['date'],
                    'set_number' => $setIndex + 1,
                    'reps_done' => $setData['reps'],
                    'to_failure' => isset($setData['failure']) && $setData['failure'] == '1',
                    'weight_kg' => $setData['weight'],
                    'rpe' => $setData['rpe'] ?? null,
                ]);
            }
        }

        AchievementService::check(Auth::id());

        return redirect()->route('progression.charts')->with('success', 'Treino registrado com sucesso!');
    }

    public function charts(Request $request)
    {
        $exerciseId = $request->get('exercise_id');
        $range = $request->get('range', 60); // Default 60 days
        $startDate = now()->subDays($range);
        
        // 1. Pegar lista de exercícios que o usuário já treinou
        $userExercises = LoadLog::where('user_id', Auth::id())
            ->join('exercises_catalog', 'load_logs.exercise_id', '=', 'exercises_catalog.id')
            ->select('exercises_catalog.id', 'exercises_catalog.name', 'exercises_catalog.muscle_group')
            ->distinct()
            ->get();

        // 2. Global Stats (Last 30 Days)
        $last30DaysLogs = LoadLog::where('user_id', Auth::id())
            ->where('log_date', '>=', now()->subDays(30))
            ->get();

        $totalVolumeMonth = $last30DaysLogs->sum(fn($l) => $l->weight_kg * $l->reps_done);
        $sessionCountMonth = $last30DaysLogs->groupBy('log_date')->count();

        // Muscle Group Distribution (All time or per session count)
        $muscleDistribution = $userExercises->groupBy('muscle_group')
            ->map(fn($group) => $group->count());

        // 3. Exercise Specific Data
        $chartData = [];
        $personalRecordValue = 0;
        $strengthGainPercent = 0;

        if ($exerciseId) {
            $logs = LoadLog::where('user_id', Auth::id())
                ->where('exercise_id', $exerciseId)
                ->where('log_date', '>=', $startDate)
                ->orderBy('log_date', 'asc')
                ->get()
                ->groupBy('log_date');

            if ($logs->isNotEmpty()) {
                $firstMaxOneRm = null;
                $currentMaxOneRm = 0;

                foreach ($logs as $date => $dayLogs) {
                    $volume = $dayLogs->sum(fn($log) => $log->weight_kg * $log->reps_done);
                    $maxOneRm = $dayLogs->max(fn($log) => $log->weight_kg / (1.0278 - 0.0278 * $log->reps_done));
                    $avgRpe = $dayLogs->avg('rpe');

                    if ($firstMaxOneRm === null) $firstMaxOneRm = $maxOneRm;
                    if ($maxOneRm > $personalRecordValue) $personalRecordValue = $maxOneRm;
                    $currentMaxOneRm = $maxOneRm;
                    
                    $chartData[] = [
                        'date' => Carbon::parse($date)->format('d/m'),
                        'volume' => round($volume, 2),
                        'one_rm' => round($maxOneRm, 2),
                        'rpe' => round($avgRpe, 1),
                    ];
                }

                if ($firstMaxOneRm > 0) {
                    $strengthGainPercent = (($currentMaxOneRm - $firstMaxOneRm) / $firstMaxOneRm) * 100;
                }
            }
        }

        return view('progression.charts', compact(
            'userExercises', 
            'chartData', 
            'exerciseId', 
            'range',
            'totalVolumeMonth',
            'sessionCountMonth',
            'muscleDistribution',
            'personalRecordValue',
            'strengthGainPercent'
        ));
    }
}
