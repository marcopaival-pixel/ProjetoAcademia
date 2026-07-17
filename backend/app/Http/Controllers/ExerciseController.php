<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $uid = (int) $user->id;

        $dateRaw = (string) $request->query('date', '');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRaw)) {
            $date = now()->format('Y-m-d');
        } else {
            $date = $dateRaw;
        }

        $notice = match ($request->query('flash')) {
            'added' => 'Exercício registrado.',
            'removed' => 'Exercício removido.',
            'updated' => 'Exercício atualizado.',
            'copied' => $request->query('n', 0) > 0
                ? 'Copiado(s) '.(int) $request->query('n').' exercício(s) de outro dia.'
                : 'Registros copiados.',
            default => '',
        };

        $editId = (int) $request->query('edit', 0);
        $editRow = null;
        if ($editId > 0) {
            $editRow = DB::table('exercise_entries')
                ->where('id', $editId)
                ->where('user_id', $uid)
                ->where('entry_date', $date)
                ->first();
            if (! $editRow) {
                $editId = 0;
            }
        }

        if ($request->isMethod('post')) {
            return $this->handlePost($request, $uid, $date);
        }

        $rows = DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $date)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $sumRow = DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $date)
            ->selectRaw('COALESCE(SUM(duration_min), 0) as dm, COALESCE(SUM(calories_burned), 0) as bk')
            ->first();
        $sumMin = (int) ($sumRow->dm ?? 0);
        $sumBurn = (int) ($sumRow->bk ?? 0);

        $pivot = \Carbon\Carbon::parse($date);
        $windowStart = $pivot->copy()->subDays(14);
        $windowEnd = $pivot->copy()->addDays(14);

        $workoutDates = DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->whereBetween('entry_date', [
                $windowStart->format('Y-m-d'),
                $windowEnd->format('Y-m-d'),
            ])
            ->select('entry_date')
            ->distinct()
            ->pluck('entry_date')
            ->all();

        $activePlans = $this->activeTrainingPlans($uid);
        $plannedWeekdays = $this->plannedTrainingWeekdays($activePlans);
        $calendarStatuses = $this->buildCalendarStatuses($windowStart, $windowEnd, $workoutDates, $plannedWeekdays);
        $plannedExercises = $this->plannedExercisesForDate($activePlans, $pivot);

        // Calcular Streak
        $streak = 0;
        $checkDate = now();
        while (true) {
            $hasEntry = DB::table('exercise_entries')
                ->where('user_id', $uid)
                ->where('entry_date', $checkDate->format('Y-m-d'))
                ->exists();
            
            if ($hasEntry) {
                $streak++;
                $checkDate = $checkDate->subDay();
            } else {
                // Se hoje não tem nada, tenta ontem
                if ($streak === 0 && $checkDate->isToday()) {
                    $checkDate = $checkDate->subDay();
                    continue;
                }
                break;
            }
        }

        return view('exercise', [
            'date' => $date,
            'rows' => $rows,
            'sumMin' => $sumMin,
            'sumBurn' => $sumBurn,
            'editRow' => $editRow,
            'notice' => $notice,
            'isPremium' => $user->hasPremiumAccess(),
            'streak' => $streak,
            'workoutDates' => $workoutDates,
            'calendarStatuses' => $calendarStatuses,
            'plannedExercises' => $plannedExercises,
            'quickScheduleDays' => $plannedWeekdays,
            'error' => session('error'),
        ]);
    }

    private function activeTrainingPlans(int $uid)
    {
        return DB::table('training_plans')
            ->where('user_id', $uid)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn(DB::raw('LOWER(status)'), ['active', 'ativo', 'publicado', 'aprovado', 'em andamento']);
            })
            ->get(['id', 'name', 'plan_label', 'days_of_week']);
    }

    private function plannedTrainingWeekdays($plans): array
    {
        $weekdays = [];
        foreach ($plans as $days) {
            $decoded = is_array($days) ? $days : json_decode((string) $days->days_of_week, true);
            if (! is_array($decoded)) {
                continue;
            }

            foreach ($decoded as $day) {
                $weekday = $this->normalizeWeekday($day);
                if ($weekday !== null) {
                    $weekdays[$weekday] = true;
                }
            }
        }

        return array_keys($weekdays);
    }

    private function plannedExercisesForDate($plans, \Carbon\Carbon $date): array
    {
        $planIds = [];
        foreach ($plans as $plan) {
            $decoded = json_decode((string) $plan->days_of_week, true);
            if (! is_array($decoded)) {
                continue;
            }

            $weekdays = array_filter(array_map(fn ($day) => $this->normalizeWeekday($day), $decoded), fn ($day) => $day !== null);
            if (in_array($date->dayOfWeek, $weekdays, true)) {
                $planIds[] = (int) $plan->id;
            }
        }

        if ($planIds === []) {
            return [];
        }

        $items = DB::table('training_plan_exercises as tpe')
            ->join('training_plans as tp', 'tp.id', '=', 'tpe.training_plan_id')
            ->join('exercises_catalog as ec', 'ec.id', '=', 'tpe.exercise_id')
            ->whereIn('tpe.training_plan_id', $planIds)
            ->orderBy('tp.id')
            ->orderBy('tpe.position')
            ->get([
                'tpe.id as plan_exercise_id',
                'tp.name as plan_name',
                'tp.plan_label',
                'ec.name',
                'ec.muscle_group',
                'ec.equipment',
                'ec.difficulty',
                'ec.instructions',
                'ec.tips',
                'ec.common_mistakes',
                'ec.video_url',
            ]);

        $sets = DB::table('exercise_sets')
            ->whereIn('training_plan_exercise_id', $items->pluck('plan_exercise_id')->all())
            ->orderBy('set_number')
            ->get()
            ->groupBy('training_plan_exercise_id');

        return $items->map(function ($item) use ($sets) {
            $targetSets = ($sets[$item->plan_exercise_id] ?? collect())->map(fn ($set) => [
                'weight' => $set->weight_target ?? '',
                'reps' => $set->reps_target ?? '',
                'rest' => (string) ($set->rest_seconds ?? 60),
                'rpe' => $set->rpe_target ?? '',
                'cadence' => $set->cadence ?? '',
                'completed' => false,
            ])->values()->all();

            return [
                'name' => $item->name,
                'plan_name' => $item->plan_name,
                'plan_label' => $item->plan_label,
                'muscle_group' => $item->muscle_group,
                'equipment' => $item->equipment,
                'difficulty' => $item->difficulty,
                'instructions' => $item->instructions,
                'tips' => $item->tips,
                'common_mistakes' => $item->common_mistakes,
                'video_url' => $item->video_url,
                'sets' => $targetSets ?: [[
                    'weight' => '',
                    'reps' => '',
                    'rest' => '60',
                    'rpe' => '',
                    'cadence' => '',
                    'completed' => false,
                ]],
            ];
        })->values()->all();
    }

    private function buildCalendarStatuses(\Carbon\Carbon $start, \Carbon\Carbon $end, array $workoutDates, array $plannedWeekdays): array
    {
        $statuses = [];
        $completed = array_fill_keys($workoutDates, true);
        $today = now()->startOfDay();

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $dateKey = $day->format('Y-m-d');
            if (isset($completed[$dateKey])) {
                $statuses[$dateKey] = 'completed';
                continue;
            }

            if (in_array($day->dayOfWeek, $plannedWeekdays, true)) {
                $statuses[$dateKey] = $day->lt($today) ? 'missed' : 'planned';
            }
        }

        return $statuses;
    }

    private function normalizeWeekday(mixed $day): ?int
    {
        if (is_numeric($day)) {
            $numeric = (int) $day;
            return $numeric >= 0 && $numeric <= 6 ? $numeric : null;
        }

        $value = mb_strtolower(trim((string) $day));
        $value = strtr($value, [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
            'é' => 'e', 'ê' => 'e',
            'í' => 'i',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ú' => 'u',
            'ç' => 'c',
        ]);

        return match ($value) {
            'domingo', 'dom', 'sunday', 'sun' => 0,
            'segunda', 'segunda-feira', 'seg', 'monday', 'mon' => 1,
            'terca', 'terca-feira', 'ter', 'tuesday', 'tue' => 2,
            'quarta', 'quarta-feira', 'qua', 'wednesday', 'wed' => 3,
            'quinta', 'quinta-feira', 'qui', 'thursday', 'thu' => 4,
            'sexta', 'sexta-feira', 'sex', 'friday', 'fri' => 5,
            'sabado', 'sab', 'saturday', 'sat' => 6,
            default => null,
        };
    }

    private function handlePost(Request $request, int $uid, string $date): RedirectResponse
    {
        $action = (string) $request->input('action', '');

        if ($action === 'copy_exercises') {
            $targetDate = (string) $request->input('target_date');
            $sourceDate = (string) $request->input('source_date');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $sourceDate)) {
                return back()->with('error', 'Datas inválidas.');
            }
            if ($sourceDate === $targetDate) {
                return back()->with('error', 'O dia de origem deve ser diferente do dia selecionado.');
            }
            $items = DB::table('exercise_entries')
                ->where('user_id', $uid)
                ->where('entry_date', $sourceDate)
                ->get();
            if ($items->isEmpty()) {
                return back()->with('error', 'Não há exercícios no dia de origem.');
            }
            foreach ($items as $it) {
                DB::table('exercise_entries')->insert([
                    'user_id' => $uid,
                    'entry_date' => $targetDate,
                    'activity_type' => $it->activity_type,
                    'duration_min' => $it->duration_min,
                    'calories_burned' => $it->calories_burned,
                    'rpe' => $it->rpe ?? null,
                    'rest_default' => $it->rest_default ?? 60,
                    'notes' => $it->notes,
                    'sets_data' => $it->sets_data,
                ]);
            }

            return redirect()->route('exercise', ['date' => $targetDate, 'flash' => 'copied', 'n' => $items->count()]);
        }

        if ($action === 'update_training_schedule') {
            $days = $request->input('days_of_week', []);
            $days = is_array($days) ? $days : [];
            $normalizedDays = [];
            foreach ($days as $day) {
                $weekday = $this->normalizeWeekday($day);
                if ($weekday !== null) {
                    $normalizedDays[] = $weekday;
                }
            }
            $normalizedDays = array_values(array_unique($normalizedDays));
            sort($normalizedDays);

            $plan = DB::table('training_plans')
                ->where('user_id', $uid)
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();

            if ($plan) {
                DB::table('training_plans')
                    ->where('id', $plan->id)
                    ->where('user_id', $uid)
                    ->update([
                        'days_of_week' => json_encode($normalizedDays),
                        'frequency' => count($normalizedDays) ?: null,
                        'status' => $plan->status ?: 'active',
                        'updated_at' => now(),
                    ]);
            } else {
                $payload = [
                    'user_id' => $uid,
                    'name' => 'Planejamento rápido',
                    'description' => 'Agenda semanal criada pelo registro de treino.',
                    'is_active' => true,
                    'status' => 'active',
                    'days_of_week' => json_encode($normalizedDays),
                    'frequency' => count($normalizedDays) ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('training_plans', 'creator_id')) {
                    $payload['creator_id'] = $uid;
                }

                DB::table('training_plans')->insert($payload);
            }

            return redirect()->route('exercise', ['date' => $date]);
        }

        if ($action === 'delete_exercise') {
            $delDate = (string) $request->input('entry_date');
            $eid = (int) $request->input('exercise_id');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $delDate) || $eid <= 0) {
                return back()->with('error', 'Dados inválidos.');
            }
            $n = DB::table('exercise_entries')
                ->where('id', $eid)
                ->where('user_id', $uid)
                ->where('entry_date', $delDate)
                ->delete();
            if ($n === 0) {
                return back()->with('error', 'Não foi possível excluir.');
            }

            return redirect()->route('exercise', ['date' => $delDate, 'flash' => 'removed']);
        }

        $date = (string) $request->input('entry_date', $date);
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return back()->with('error', 'Data inválida.');
        }
        $type = trim((string) $request->input('activity_type'));
        $dur = (int) $request->input('duration_min', 0);
        $cb = $request->input('calories_burned');
        $cbVal = $cb === '' || $cb === null ? null : (int) $cb;
        $rpe = (int) $request->input('rpe', 0);
        $rest = (int) $request->input('rest_default', 60);
        $notes = trim((string) $request->input('notes', ''));
        $exEditId = (int) $request->input('exercise_edit_id', 0);

        if ($type === '') {
            return back()->with('error', 'Informe o tipo de atividade.');
        }
        if ($dur < 0 || $dur > 1440) {
            return back()->with('error', 'Duração inválida (minutos).');
        }

        $notesVal = $notes === '' ? null : substr($notes, 0, 500);
        $setsData = $request->input('sets_data'); // JSON string from frontend

        if ($exEditId > 0) {
            $own = DB::table('exercise_entries')
                ->where('id', $exEditId)
                ->where('user_id', $uid)
                ->where('entry_date', $date)
                ->exists();
            if (! $own) {
                return back()->with('error', 'Registro não encontrado.');
            }
            DB::table('exercise_entries')->where('id', $exEditId)->update([
                'activity_type' => $type,
                'duration_min' => $dur,
                'calories_burned' => $cbVal,
                'rpe' => $rpe > 0 ? $rpe : null,
                'rest_default' => $rest,
                'sets_data' => $setsData,
                'notes' => $notesVal,
            ]);

            return redirect()->route('exercise', ['date' => $date, 'flash' => 'updated']);
        }

        DB::table('exercise_entries')->insert([
            'user_id' => $uid,
            'entry_date' => $date,
            'activity_type' => $type,
            'duration_min' => $dur,
            'calories_burned' => $cbVal,
            'rpe' => $rpe > 0 ? $rpe : null,
            'rest_default' => $rest,
            'sets_data' => $setsData,
            'notes' => $notesVal,
            'created_at' => now(),
        ]);

        return redirect()->route('exercise', ['date' => $date, 'flash' => 'added']);
    }

    // --- API Methods for Advanced HUD ---

    public function apiSearch(Request $request)
    {
        $q = $request->query('q', '');
        if (strlen($q) < 2) {
            // Sugerir favoritos ou últimos usados se vazio?
            $recent = DB::table('exercise_entries')
                ->where('user_id', $request->user()->id)
                ->select('activity_type as name')
                ->distinct()
                ->limit(5)
                ->get();
            return response()->json(['results' => [], 'recent' => $recent]);
        }

        $results = DB::table('exercises_catalog')
            ->where('name', 'like', "%{$q}%")
            ->where('is_active', true)
            ->limit(10)
            ->get(['name', 'muscle_group', 'equipment', 'difficulty', 'instructions', 'tips', 'common_mistakes', 'video_url']);

        return response()->json(['results' => $results]);
    }

    public function apiListAll(Request $request)
    {
        $exercises = DB::table('exercises_catalog')
            ->where('is_active', true)
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get(['name', 'muscle_group', 'equipment', 'difficulty', 'instructions', 'tips', 'common_mistakes', 'video_url']);

        return response()->json(['exercises' => $exercises]);
    }

    public function apiHistory(Request $request)
    {
        $exercise = $request->query('exercise', '');
        if (!$exercise) return response()->json(['history' => []]);

        $history = DB::table('exercise_entries')
            ->where('user_id', $request->user()->id)
            ->where('activity_type', $exercise)
            ->orderBy('entry_date', 'desc')
            ->limit(3)
            ->get(['entry_date', 'sets_data', 'calories_burned', 'rpe']);

        return response()->json(['history' => $history]);
    }

    public function apiLastWorkout(Request $request)
    {
        $last = DB::table('exercise_entries')
            ->where('user_id', $request->user()->id)
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$last) return response()->json(['error' => 'Nenhum treino anterior encontrado.'], 404);

        return response()->json(['last' => $last]);
    }

    public function apiCalculateCalories(Request $request)
    {
        $duration = (int) $request->input('duration', 0);
        $exercise = $request->input('exercise', '');
        $rpe = (int) $request->input('rpe', 5);
        $userWeight = $request->user()->profile->weight_kg ?? 70;

        // Simplistic MET-based calculation
        // MET varies by exercise type. For generic weight training it's around 3.5 - 6.0
        $met = 5.0; // Default
        if ($rpe > 8) $met = 7.0;
        elseif ($rpe < 4) $met = 3.0;

        $calories = round(($met * 3.5 * $userWeight / 200) * $duration);

        return response()->json(['calories' => $calories]);
    }

    public function apiSync(Request $request)
    {
        $uid = $request->user()->id;
        $id = (int) $request->input('id', 0);
        $data = $request->only(['activity_type', 'duration_min', 'calories_burned', 'rpe', 'rest_default', 'sets_data', 'notes', 'entry_date']);
        
        if ($id > 0) {
            DB::table('exercise_entries')
                ->where('id', $id)
                ->where('user_id', $uid)
                ->update(array_merge($data, ['updated_at' => now()]));
            return response()->json(['success' => true, 'id' => $id]);
        } else {
            $newId = DB::table('exercise_entries')->insertGetId(array_merge($data, [
                'user_id' => $uid,
                'created_at' => now(),
                'updated_at' => now()
            ]));
            return response()->json(['success' => true, 'id' => $newId]);
        }
    }
}
