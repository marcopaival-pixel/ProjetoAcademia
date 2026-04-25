<?php

namespace App\Http\Controllers;

use App\Models\TrainingPlan;
use App\Models\LoadLog;
use App\Models\TrainingPlanExercise;
use App\Services\ProgressionService;
use App\Services\AchievementService;
use App\Models\BodyAssessment;
use App\Models\WeightEntry;
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
        
        $isPremium = Auth::user()->hasPremiumAccess();
        
        // Buscar logs anteriores e calcular sugestão de carga
        foreach ($plan->exercises as $exercise) {
            $lastLog = LoadLog::where('user_id', Auth::id())
                ->where('exercise_id', $exercise->exercise_id)
                ->orderBy('log_date', 'desc')
                ->first();

            $exercise->last_log = $lastLog;
            
            if ($isPremium) {
                // Sugestão Inteligente (Premium)
                $targetReps = $exercise->sets->first()->reps_target ?? 10;
                $exercise->suggestion = ProgressionService::suggestLoad(
                    Auth::id(),
                    $exercise->exercise_id,
                    $lastLog->weight_kg ?? ($exercise->sets->first()->weight_target ?? 0),
                    $targetReps
                );
            } else {
                // Placeholder para Non-Premium
                $exercise->suggestion = [
                    'suggested_weight' => $lastLog->weight_kg ?? ($exercise->sets->first()->weight_target ?? 0),
                    'message' => '🔒 Upgrade para Sugestões IA',
                    'indicator' => 'locked'
                ];
            }
        }

        return view('progression.session-log', compact('plan'));
    }

    public function storeLog(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'logs' => 'required|array|min:1',
            'logs.*.training_plan_exercise_id' => 'required|exists:training_plan_exercises,id',
            'logs.*.exercise_id' => 'required|exists:exercises_catalog,id',
            'logs.*.sets' => 'required|array|min:1',
            'logs.*.sets.*.rpe' => 'nullable|integer|min:1|max:10',
        ], [
            'date.required' => 'A data do registro é obrigatória.',
            'logs.required' => 'Nenhum dado de exercício foi enviado para consolidação.',
            'logs.min' => 'Você deve registrar pelo menos um exercício realizado.',
            'logs.*.sets.required' => 'Você deve preencher pelo menos uma série para cada exercício treinado.',
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

        // 2. Global Stats (Last 30 Days) — agregação na BD (evita carregar milhares de linhas em RAM)
        $uid = Auth::id();
        $since30 = now()->subDays(30);

        $totalVolumeMonth = (float) LoadLog::where('user_id', $uid)
            ->where('log_date', '>=', $since30)
            ->selectRaw('COALESCE(SUM(weight_kg * reps_done), 0) as v')
            ->value('v');

        $sessionCountMonth = (int) LoadLog::where('user_id', $uid)
            ->where('log_date', '>=', $since30)
            ->distinct()
            ->count('log_date');

        // Muscle Group Distribution (Premium: Volume Load / Free: Exercise Count)
        $muscleDistribution = [];
        $isPremium = Auth::user()->hasPremiumAccess();

        if ($isPremium) {
            // Volume total por músculo (Soma de Peso x Reps) nos últimos 30 dias
            $muscleDistribution = LoadLog::where('user_id', Auth::id())
                ->join('exercises_catalog', 'load_logs.exercise_id', '=', 'exercises_catalog.id')
                ->where('log_date', '>=', now()->subDays(30))
                ->select('exercises_catalog.muscle_group', DB::raw('SUM(weight_kg * reps_done) as volume'))
                ->groupBy('exercises_catalog.muscle_group')
                ->pluck('volume', 'muscle_group')
                ->toArray();
        } else {
            // Apenas contagem de exercícios para usuários free
            $muscleDistribution = $userExercises->groupBy('muscle_group')
                ->map(fn($group) => $group->count())
                ->toArray();
        }

        // 3. Exercise Specific Data
        $chartData = [];
        $personalRecordValue = 0;
        $strengthGainPercent = 0;

        if ($exerciseId) {
            $dailyStats = LoadLog::where('user_id', Auth::id())
                ->where('exercise_id', $exerciseId)
                ->where('log_date', '>=', $startDate)
                ->where('reps_done', '>', 0)
                ->selectRaw('
                    log_date,
                    SUM(weight_kg * reps_done) as volume,
                    AVG(rpe) as avg_rpe,
                    MAX(weight_kg / (1.0278 - 0.0278 * reps_done)) as max_one_rm
                ')
                ->groupBy('log_date')
                ->orderBy('log_date', 'asc')
                ->get();

            if ($dailyStats->isNotEmpty()) {
                $firstMaxOneRm = (float) $dailyStats->first()->max_one_rm;
                $currentMaxOneRm = (float) $dailyStats->last()->max_one_rm;
                $personalRecordValue = (float) $dailyStats->max('max_one_rm');

                foreach ($dailyStats as $row) {
                    $chartData[] = [
                        'date' => Carbon::parse($row->log_date)->format('d/m'),
                        'volume' => round((float) $row->volume, 2),
                        'one_rm' => round((float) $row->max_one_rm, 2),
                        'rpe' => round((float) ($row->avg_rpe ?? 0), 1),
                    ];
                }

                if ($firstMaxOneRm > 0) {
                    $strengthGainPercent = (($currentMaxOneRm - $firstMaxOneRm) / $firstMaxOneRm) * 100;
                }
            }
        }

        // 4. Body Composition Evolution (Weight vs BF)
        $compositionData = BodyAssessment::where('user_id', Auth::id())
            ->whereNotNull('bf_percent')
            ->where('assessment_date', '>=', now()->subDays(180)) // Last 6 months
            ->orderBy('assessment_date', 'asc')
            ->get()
            ->map(fn($a) => [
                'date' => Carbon::parse($a->assessment_date)->format('d/m'),
                'weight' => $a->weight_kg,
                'bf' => $a->bf_percent
            ]);

        return view('progression.charts', compact(
            'userExercises', 
            'chartData', 
            'exerciseId', 
            'range',
            'totalVolumeMonth',
            'sessionCountMonth',
            'muscleDistribution',
            'personalRecordValue',
            'strengthGainPercent',
            'compositionData',
            'isPremium'
        ));
    }
}
