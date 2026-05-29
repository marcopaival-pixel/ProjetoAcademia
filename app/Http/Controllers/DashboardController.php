<?php

namespace App\Http\Controllers;

use App\Services\Nutrition;
use App\Models\UserProfile;
use App\Models\FoodEntry;
use App\Models\ExerciseEntry;
use App\Models\WeightEntry;
use App\Models\WaterEntry;
use App\Models\TrainingPlan;
use App\Models\LoadLog;
use App\Models\BodyAssessment;
use App\Models\HealthAlert;
use App\Services\ProgressionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $activeRole = session('active_role');

        // Redirecionamento baseado em perfil
        if ($user->isAdministrator()) {
            // Se for admin mas solicitou ver o painel de utilizador ou escolheu um perfil de utilizador específico
            if (($activeRole === 'admin' || !$activeRole) && !$request->has('view_as_user')) {
                return redirect()->route('admin.dashboard');
            }
        }

        if ($activeRole === 'professional' || ($user->hasRole(['professional', 'instructor']) && !$activeRole)) {
            return redirect()->route('professional.dashboard');
        }

        if (in_array($activeRole, ['manager', 'receptionist', 'supervisor']) || ($user->hasRole(['manager', 'receptionist', 'supervisor']) && !$activeRole)) {
            return redirect()->route('agenda.index');
        }

        if ($activeRole === 'paciente' || ($user->hasRole('paciente') && !$user->hasRole('aluno') && !$activeRole)) {
            return redirect()->route('patient.unified.dashboard');
        }

        $uid = (int) $user->id;
        $isPremium = $user->hasPremiumAccess();
        $today = now()->format('Y-m-d');

        if ($request->isMethod('post')) {
            // Invalida o cache do dashboard ao salvar novos dados
            Cache::forget("user_dashboard_stats_{$uid}");

            // Prioridade para o valor manual, se vazio usa o botão rápido clicado
            $valQuick = $request->input('water_add');
            $valCustom = $request->input('water_add_custom');
            $amount = (int) ($valCustom ?: $valQuick);

            if ($amount > 0) {
                WaterEntry::create([
                    'user_id' => $uid,
                    'entry_date' => $today,
                    'amount_ml' => $amount,
                    'drank_at' => now(),
                    'source' => 'quick_tap_dashboard',
                ]);

                return redirect()->route('dashboard');
            }
            
            if ($request->has('water_delete')) {
                $id = (int) $request->input('water_delete');
                WaterEntry::where('id', $id)
                    ->where('user_id', $uid)
                    ->delete();

                return redirect()->route('dashboard');
            }
        }

        $stats = Cache::remember("user_dashboard_stats_{$uid}", now()->addMinutes(5), function() use ($uid, $user, $isPremium, $today) {
            $prof = UserProfile::where('user_id', $uid)->first();
            $profArr = $prof ? $prof->toArray() : [];
            $calorieTarget = isset($prof?->daily_calorie_target) && $prof->daily_calorie_target !== null
                ? (int) $prof->daily_calorie_target : null;
            $waterTarget = isset($prof?->water_target_ml) && $prof->water_target_ml !== null
                ? (int) $prof->water_target_ml : 2000;
            $macroTargets = Nutrition::macroTargetsForDisplay($isPremium, $profArr);
            $hasMacroTargets = $isPremium
                ? (($macroTargets['p'] ?? 0) > 0 || ($macroTargets['c'] ?? 0) > 0 || ($macroTargets['f'] ?? 0) > 0)
                : ($calorieTarget !== null);

            $foodSums = FoodEntry::where('user_id', $uid)
                ->where('entry_date', $today)
                ->selectRaw('COALESCE(SUM(calories), 0) as c, COALESCE(SUM(protein_g), 0) as p, COALESCE(SUM(carbs_g), 0) as cb, COALESCE(SUM(fat_g), 0) as f')
                ->first();
                
            $consumed = (int) ($foodSums->c ?? 0);
            $sumProt = (float) ($foodSums->p ?? 0);
            $sumCarb = (float) ($foodSums->cb ?? 0);
            $sumFat = (float) ($foodSums->f ?? 0);

            $burned = (int) ExerciseEntry::where('user_id', $uid)
                ->where('entry_date', $today)
                ->whereNotNull('calories_burned')
                ->sum('calories_burned');

            $remaining = $calorieTarget !== null ? $calorieTarget - $consumed + $burned : null;

            $lastWeight = WeightEntry::where('user_id', $uid)
                ->select('weight_kg', 'weighed_at')
                ->orderByDesc('weighed_at')
                ->first();

            $waterConsumed = (int) WaterEntry::where('user_id', $uid)
                ->whereDate('entry_date', $today)
                ->sum('amount_ml');

            // Coleta de Atividades Recentes
            $recentFood = FoodEntry::where('user_id', $uid)
                ->select('food_name as title', 'calories', 'created_at as time')
                ->orderByDesc('created_at')
                ->limit(3)->get()->map(fn($f) => (object)['type' => 'food', 'title' => $f->title, 'value' => $f->calories . ' kcal', 'time' => $f->time]);
            $recentExercise = ExerciseEntry::where('user_id', $uid)
                ->select('activity_type as title', 'calories_burned as value', 'created_at as time')
                ->orderByDesc('created_at')
                ->limit(3)->get()->map(fn($e) => (object)['type' => 'exercise', 'title' => $e->title ?? 'Treino', 'value' => ($e->value ?? 0) . ' kcal', 'time' => $e->time]);
            $recentWeight = WeightEntry::where('user_id', $uid)
                ->select('weight_kg as value', 'weighed_at as time')
                ->orderByDesc('weighed_at')
                ->limit(3)->get()->map(fn($w) => (object)['type' => 'weight', 'title' => 'Nova Pesagem', 'value' => $w->value . ' kg', 'time' => $w->time]);

            $recentActivities = collect()->concat($recentFood)->concat($recentExercise)->concat($recentWeight)
                ->sortByDesc('time')->values()->take(4);

            $nextTraining = TrainingPlan::where('user_id', $uid)->where('is_active', true)->first();

            $prsCount = LoadLog::query()->from('load_logs as l1')
                ->join('load_logs as l2', function($join) { $join->on('l1.exercise_id', '=', 'l2.exercise_id')->on('l1.user_id', '=', 'l2.user_id')->on('l1.log_date', '>', 'l2.log_date'); })
                ->where('l1.user_id', $uid)->where('l1.log_date', '>=', now()->subDays(30))
                ->select('l1.exercise_id', 'l1.log_date')->groupBy('l1.exercise_id', 'l1.log_date')
                ->havingRaw('MAX(l1.one_rm) > MAX(l2.one_rm)')
                ->count();

            $latestAssessment = BodyAssessment::where('user_id', $uid)->whereNotNull('bf_percent')->orderByDesc('assessment_date')->first();
            $topExercisePR = LoadLog::where('load_logs.user_id', $uid)
                ->join('exercises_catalog', 'load_logs.exercise_id', '=', 'exercises_catalog.id')
                ->select('load_logs.*', 'exercises_catalog.name as exercise_name')
                ->orderByDesc('one_rm')->first();

            $neuralPrediction = ($isPremium && $topExercisePR) ? ProgressionService::suggestLoad($uid, $topExercisePR->exercise_id, (float)$topExercisePR->weight_kg, $topExercisePR->reps_done) : null;

            $setupChecklist = [
                'profile' => ['label' => 'Definir Metas Corporais', 'done' => ($lastWeight && $prof?->height_cm && $prof?->target_weight_kg), 'route' => route('profile'), 'premium' => false],
                'meal' => ['label' => 'Registrar Primeira Refeição', 'done' => FoodEntry::where('user_id', $uid)->exists(), 'route' => route('diary'), 'premium' => false],
                'workout' => ['label' => 'Realizar Primeiro Treino', 'done' => ExerciseEntry::where('user_id', $uid)->exists(), 'route' => route('exercise'), 'premium' => false],
                'ai_chat' => ['label' => 'Conversar com o NexBot (IA)', 'done' => \App\Models\AIChat::where('user_id', $uid)->exists(), 'route' => route('chat.page'), 'premium' => true],
                'body_analysis' => ['label' => 'Fazer Análise Corporal IA', 'done' => BodyAssessment::where('user_id', $uid)->exists(), 'route' => route('body-analysis.index'), 'premium' => true]
            ];

            return [
                'calorieTarget' => $calorieTarget,
                'waterTarget' => $waterTarget,
                'macroTargets' => $macroTargets,
                'hasMacroTargets' => $hasMacroTargets,
                'consumed' => $consumed,
                'sumProt' => $sumProt,
                'sumCarb' => $sumCarb,
                'sumFat' => $sumFat,
                'burned' => $burned,
                'remaining' => $remaining,
                'lastWeight' => $lastWeight,
                'waterConsumed' => $waterConsumed,
                'recentActivities' => $recentActivities,
                'nextTraining' => $nextTraining,
                'prsCount' => $prsCount,
                'latestAssessment' => $latestAssessment,
                'topExercisePR' => $topExercisePR,
                'neuralPrediction' => $neuralPrediction,
                'setupChecklist' => $setupChecklist,
                'linkedProfessional' => $user->professionals()->first(),
                'pendingRequest' => $user->sentRequests()->where('status', 'pending')->first(),
                'healthAlerts' => HealthAlert::where('user_id', $uid)->where('is_read', false)->latest()->take(3)->get(),
                'performanceStatus' => app(\App\Services\PerformanceAnalysisService::class)->getUserStatus($user),
                'trainingCount' => TrainingPlan::where('user_id', $uid)->count(),
                'assessmentsCount' => BodyAssessment::where('user_id', $uid)->count(),
                'communityPosts' => \App\Models\CommunityPost::with(['user', 'reactions', 'comments', 'media'])
                    ->where('status', 'approved')
                    ->where('visibility', 'public')
                    ->latest()
                    ->take(5)
                    ->get(),
                'aiCreditWallet' => app(\App\Services\AiCreditService::class)->getWallet($user),
                'evolutionStatus' => app(\App\Services\EvolutionStatusService::class)->getEvolutionStatus($user),
                'systemAccessLinks' => $user->systemAccessLinks()->get(),
            ];
        });

        // Insights de IA (calculados fora do cache pois podem depender do tempo exato ou randomização)
        if ($isPremium) {
            $aiInsight = "Analisando seu padrão biométrico... ";
            if ($stats['waterConsumed'] < ($stats['waterTarget'] * 0.5)) {
                $aiInsight .= "Sua hidratação está em nível crítico (" . round(($stats['waterConsumed'] / $stats['waterTarget']) * 100) . "%). Isso pode reduzir sua força em até 20% no próximo treino.";
            } elseif ($stats['prsCount'] > 0) {
                $aiInsight .= "Pico de performance detectado! Você superou " . $stats['prsCount'] . " marcas pessoais recentemente. Considere aumentar a ingestão proteica para otimizar a síntese muscular.";
            } else {
                $aiInsight .= collect([
                    "Sua variabilidade de frequência cardíaca indica que você está pronto para um treino de alta intensidade hoje.",
                    "Otimize seu ciclo circadiano: procure ingerir sua última grande refeição 3h antes de dormir.",
                    "Baseado no seu volume de treino, o magnésio pode ser um aliado na recuperação neuromuscular hoje."
                ])->random();
            }
        } else {
            $aiInsight = "Continue mantendo o foco nos seus objetivos!";
            if ($stats['waterConsumed'] < ($stats['waterTarget'] * 0.5)) {
                $aiInsight = "Sua hidratação está baixa hoje. Beba mais água para manter o foco.";
            } elseif ($stats['prsCount'] > 0) {
                $aiInsight = "Parabéns pelos novos recordes! Continue evoluindo.";
            } else {
                $aiInsight = collect([
                    "Beber mais água durante o treino aumenta o rendimento.",
                    "Consistência é melhor que perfeição. Continue firme!",
                    "O descanso é fundamental para o crescimento muscular."
                ])->random();
            }
        }

        $completedTasks = collect($stats['setupChecklist'])->where('done', true)->count();
        $totalTasks = count($stats['setupChecklist']);
        $setupPercentage = ($completedTasks / $totalTasks) * 100;
        $showChecklist = $setupPercentage < 100;

        return view('dashboard', array_merge($stats, [
            'today' => $today,
            'isPremium' => $isPremium,
            'aiInsight' => $aiInsight,
            'setupPercentage' => $setupPercentage,
            'showChecklist' => $showChecklist
        ]));
    }
}
