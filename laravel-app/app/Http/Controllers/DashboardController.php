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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $uid = (int) $user->id;
        $isPremium = $user->hasPremiumAccess();
        $today = now()->format('Y-m-d');

        if ($request->isMethod('post')) {
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
            ->orderByDesc('weighed_at')
            ->first();

        try {
            $waterEntriesToday = $user->waterEntries()
                ->whereDate('entry_date', $today)
                ->get();
        } catch (\Illuminate\Database\QueryException $e) {
            // Se as colunas dratk_at/source não existirem
            if (str_contains($e->getMessage(), 'Unknown column \'drank_at\'')) {
                \Illuminate\Support\Facades\Schema::table('water_entries', function ($table) {
                    $table->timestamp('drank_at')->nullable()->after('entry_date');
                    $table->string('source')->nullable()->after('drank_at');
                });
                
                $waterEntriesToday = $user->waterEntries()
                    ->whereDate('entry_date', $today)
                    ->get();
            } else {
                 $waterEntriesToday = collect();
            }
        }

        $waterConsumed = $waterEntriesToday->sum('amount_ml');

        // Atividades Recentes
        $recentActivities = collect();
        $recentFood = FoodEntry::where('user_id', $uid)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(fn($f) => (object)['type' => 'food', 'title' => $f->food_name, 'value' => $f->calories . ' kcal', 'time' => $f->created_at]);
            
        $recentExercise = ExerciseEntry::where('user_id', $uid)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(fn($e) => (object)['type' => 'exercise', 'title' => $e->activity_type ?? 'Treino', 'value' => ($e->calories_burned ?? 0) . ' kcal', 'time' => $e->created_at]);
            
        $recentWeight = WeightEntry::where('user_id', $uid)
            ->orderByDesc('weighed_at')
            ->limit(3)
            ->get()
            ->map(fn($w) => (object)['type' => 'weight', 'title' => 'Nova Pesagem', 'value' => $w->weight_kg . ' kg', 'time' => $w->weighed_at]);

        $recentActivities = $recentActivities->concat($recentFood)->concat($recentExercise)->concat($recentWeight)
            ->sortByDesc('time')
            ->values()
            ->take(4);

        // Próximo Treino
        $nextTraining = TrainingPlan::where('user_id', $uid)
            ->where('is_active', true)
            ->first();

        // Contagem de Mensagens Internas (Novo Sistema com Auto-Healing)
        try {
            $unreadEmails = \App\Models\InternalEmail::where('destinatario_id', $uid)
                ->where('lida', false)
                ->whereNull('excluded_at_receiver')
                ->count();
        } catch (\Illuminate\Database\QueryException $e) {
            // Se a tabela não existir, tentamos criar agora para não quebrar o sistema
            if (str_contains($e->getMessage(), '1146') || str_contains($e->getMessage(), 'Table \'internal_emails\' doesn\'t exist')) {
                \Illuminate\Support\Facades\Schema::create('internal_emails', function ($table) {
                    $table->id();
                    $table->unsignedInteger('remetente_id');
                    $table->unsignedInteger('destinatario_id');
                    $table->string('assunto', 200);
                    $table->text('mensagem');
                    $table->boolean('lida')->default(false);
                    $table->timestamp('data_envio')->nullable();
                    $table->timestamp('data_leitura')->nullable();
                    $table->timestamp('excluded_at_sender')->nullable();
                    $table->timestamp('excluded_at_receiver')->nullable();
                    $table->enum('status', ['draft', 'outbox', 'sent', 'failed'])->default('sent');
                    $table->unsignedBigInteger('parent_id')->nullable();
                    $table->boolean('is_system')->default(false);
                    $table->timestamps();
                });
                
                // Recalcular com a tabela recém-criada
                $unreadEmails = \App\Models\InternalEmail::where('destinatario_id', $uid)
                    ->where('lida', false)
                    ->whereNull('excluded_at_receiver')
                    ->count();
            } else {
                $unreadEmails = 0;
            }
        }

        // Personal Records (PRs) in the last 30 days
        $prsCount = LoadLog::query()->from('load_logs as l1')
            ->join('load_logs as l2', function($join) {
                $join->on('l1.exercise_id', '=', 'l2.exercise_id')
                     ->on('l1.user_id', '=', 'l2.user_id')
                     ->on('l1.log_date', '>', 'l2.log_date');
            })
            ->where('l1.user_id', $uid)
            ->where('l1.log_date', '>=', now()->subDays(30))
            ->select('l1.exercise_id', 'l1.log_date')
            ->groupBy('l1.exercise_id', 'l1.log_date')
            ->havingRaw('MAX(l1.weight_kg / (1.0278 - 0.0278 * l1.reps_done)) > MAX(l2.weight_kg / (1.0278 - 0.0278 * l2.reps_done))')
            ->get()
            ->count();

        // AI Insight Placeholder (Randomized for demo)
        $insights = [
            "Beber mais água durante o treino aumenta o rendimento em até 15%.",
            "Consumir proteínas após o treino ajuda na reconstrução muscular.",
            "Um sono de 8h é essencial para a recuperação neuro-muscular.",
            "Consistência é melhor que perfeição. Continue firme!",
            "Tente variar os exercícios de cardio para evitar o platô."
        ];
        $aiInsight = $insights[array_rand($insights)];

        return view('dashboard', compact(
            'today',
            'calorieTarget',
            'waterTarget',
            'macroTargets',
            'hasMacroTargets',
            'consumed',
            'sumProt',
            'sumCarb',
            'sumFat',
            'burned',
            'remaining',
            'lastWeight',
            'waterConsumed',
            'waterEntriesToday',
            'isPremium',
            'recentActivities',
            'nextTraining',
            'unreadEmails',
            'prsCount',
            'aiInsight'
        ));
    }
}
