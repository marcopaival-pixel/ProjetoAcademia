<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class ProfessionalReportAggregator
{
    /**
     * Gera um relatório de performance agregada de todos os alunos de um profissional.
     */
    public static function studentPerformance(int $professionalId, Carbon $start, Carbon $end): array
    {
        // 1. Buscar IDs dos alunos vinculados
        $studentIds = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('status', 'Sim')
            ->pluck('user_id');

        if ($studentIds->isEmpty()) {
            return [
                'total_students' => 0,
                'active_students' => 0,
                'avg_adherence_food' => 0,
                'avg_adherence_training' => 0,
                'total_workouts' => 0,
                'students_data' => []
            ];
        }

        // 2. Agregação de Treinos
        $workoutStats = DB::table('exercise_entries')
            ->whereIn('user_id', $studentIds)
            ->whereBetween('entry_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('user_id, COUNT(*) as count, SUM(duration_min) as total_min')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // 3. Agregação de Alimentação (Dias registrados)
        $foodStats = DB::table('food_entries')
            ->whereIn('user_id', $studentIds)
            ->whereBetween('entry_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('user_id, COUNT(DISTINCT entry_date) as active_days')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // 4. Buscar Informações dos Alunos
        $students = User::whereIn('id', $studentIds)
            ->select('id', 'name', 'email', 'avatar')
            ->get();

        $rangeDays = $start->diffInDays($end) + 1;
        $totalWorkouts = 0;
        $sumAdherenceFood = 0;
        $sumAdherenceTraining = 0;

        $studentsData = $students->map(function($student) use ($workoutStats, $foodStats, $rangeDays, &$totalWorkouts, &$sumAdherenceFood, &$sumAdherenceTraining) {
            $w = $workoutStats->get($student->id);
            $f = $foodStats->get($student->id);

            $workoutsCount = $w->count ?? 0;
            $foodDays = $f->active_days ?? 0;

            $totalWorkouts += $workoutsCount;
            
            $adherenceFood = round(($foodDays / $rangeDays) * 100);
            $adherenceTraining = round(($workoutsCount / ($rangeDays * 0.7)) * 100); // Meta arbitrária de 70% dos dias
            if ($adherenceTraining > 100) $adherenceTraining = 100;

            $sumAdherenceFood += $adherenceFood;
            $sumAdherenceTraining += $adherenceTraining;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'avatar' => $student->avatar,
                'workouts' => $workoutsCount,
                'food_days' => $foodDays,
                'adherence_food' => $adherenceFood,
                'adherence_training' => $adherenceTraining,
            ];
        });

        $count = $students->count();

        return [
            'total_students' => $count,
            'active_students' => $studentsData->where('food_days', '>', 0)->count(),
            'avg_adherence_food' => $count > 0 ? round($sumAdherenceFood / $count) : 0,
            'avg_adherence_training' => $count > 0 ? round($sumAdherenceTraining / $count) : 0,
            'total_workouts' => $totalWorkouts,
            'students_data' => $studentsData->sortByDesc('adherence_training')->values()->toArray(),
        ];
    }

    public static function studentFinancials(int $professionalId): array
    {
        // 1. Buscar alunos vinculados
        $studentIds = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('status', 'Sim')
            ->pluck('user_id');

        if ($studentIds->isEmpty()) {
            return [
                'total_active_subscriptions' => 0,
                'total_pending' => 0,
                'total_revenue_estimated' => 0,
                'subscriptions' => []
            ];
        }

        // 2. Buscar assinaturas dos alunos
        $subscriptions = \App\Models\Subscription::with(['user', 'plan'])
            ->whereIn('user_id', $studentIds)
            ->get();

        $activeCount = $subscriptions->whereIn('status', ['active', 'ATIVO'])->count();
        $pendingCount = $subscriptions->whereIn('status', ['pending', 'PENDENTE'])->count();
        $totalRevenue = $subscriptions->whereIn('status', ['active', 'ATIVO'])->sum(fn($s) => $s->plan->price ?? 0);

        return [
            'total_active_subscriptions' => $activeCount,
            'total_pending' => $pendingCount,
            'total_revenue_estimated' => $totalRevenue,
            'subscriptions' => $subscriptions->sortByDesc('created_at')->values()->toArray(),
        ];
    }

    public static function studentComparison(int $professionalId): array
    {
        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        $studentIds = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('status', 'Sim')
            ->pluck('user_id');

        if ($studentIds->isEmpty()) {
            return ['current' => [], 'previous' => [], 'delta' => []];
        }

        $currentStats = DB::table('exercise_entries')
            ->whereIn('user_id', $studentIds)
            ->whereBetween('entry_date', [$thisMonthStart->toDateString(), $now->toDateString()])
            ->selectRaw('COUNT(*) as workouts, SUM(duration_min) as minutes')
            ->first();

        $previousStats = DB::table('exercise_entries')
            ->whereIn('user_id', $studentIds)
            ->whereBetween('entry_date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])
            ->selectRaw('COUNT(*) as workouts, SUM(duration_min) as minutes')
            ->first();

        $currentStudents = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('status', 'Sim')
            ->where('data_cadastro', '<=', $now->toDateString())
            ->count();

        $previousStudents = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('status', 'Sim')
            ->where('data_cadastro', '<=', $lastMonthEnd->toDateString())
            ->count();

        return [
            'current' => [
                'workouts' => (int) $currentStats->workouts,
                'minutes' => (int) $currentStats->minutes,
                'students' => $currentStudents,
            ],
            'previous' => [
                'workouts' => (int) $previousStats->workouts,
                'minutes' => (int) $previousStats->minutes,
                'students' => $previousStudents,
            ],
            'delta' => [
                'workouts' => $previousStats->workouts > 0 ? round((($currentStats->workouts - $previousStats->workouts) / $previousStats->workouts) * 100) : 100,
                'students' => $previousStudents > 0 ? round((($currentStudents - $previousStudents) / $previousStudents) * 100) : 100,
            ]
        ];
    }

    public static function kpiSummary(int $professionalId): array
    {
        $studentIds = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('status', 'Sim')
            ->pluck('user_id');

        if ($studentIds->isEmpty()) {
            return [
                'retention_rate' => 0,
                'avg_sessions_per_student' => 0,
                'active_plans_percentage' => 0,
                'growth_score' => 0
            ];
        }

        $totalStudents = $studentIds->count();
        
        // Retention: Students who registered > 30 days ago and are still active
        $retainedStudents = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('status', 'Sim')
            ->where('data_cadastro', '<=', now()->subDays(30)->toDateString())
            ->count();
        
        $totalOldStudents = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('data_cadastro', '<=', now()->subDays(30)->toDateString())
            ->count();

        $retentionRate = $totalOldStudents > 0 ? round(($retainedStudents / $totalOldStudents) * 100) : 100;

        // Average sessions per student (Last 30 days)
        $totalSessions = DB::table('exercise_entries')
            ->whereIn('user_id', $studentIds)
            ->where('entry_date', '>=', now()->subDays(30)->toDateString())
            ->count();
        
        $avgSessions = $totalStudents > 0 ? round($totalSessions / $totalStudents, 1) : 0;

        // Active Plans %
        $activePlansCount = DB::table('subscriptions')
            ->whereIn('user_id', $studentIds)
            ->whereIn('status', ['active', 'ATIVO'])
            ->count();
        
        $plansPercentage = $totalStudents > 0 ? round(($activePlansCount / $totalStudents) * 100) : 0;

        return [
            'retention_rate' => $retentionRate,
            'avg_sessions_per_student' => $avgSessions,
            'active_plans_percentage' => $plansPercentage,
            'growth_score' => round(($totalStudents / 10) * 10) // Score fictício baseado em volume
        ];
    }

    public static function managementSummary(int $professionalId): array
    {
        $studentIds = DB::table('pacientes')
            ->where('profissional_id', $professionalId)
            ->where('status', 'Sim')
            ->pluck('user_id');

        if ($studentIds->isEmpty()) {
            return ['churn_risk' => [], 'recent_activity' => []];
        }

        // Churn Risk: No workout in last 15 days
        $inactiveIds = DB::table('exercise_entries')
            ->whereIn('user_id', $studentIds)
            ->where('entry_date', '>=', now()->subDays(15)->toDateString())
            ->pluck('user_id')
            ->toArray();
        
        $churnRiskIds = array_diff($studentIds->toArray(), $inactiveIds);
        
        $churnRisk = User::whereIn('id', $churnRiskIds)
            ->select('id', 'name', 'email', 'avatar')
            ->get()
            ->map(function($user) use ($professionalId) {
                $lastWorkout = DB::table('exercise_entries')
                    ->where('user_id', $user->id)
                    ->orderByDesc('entry_date')
                    ->value('entry_date');
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'last_activity' => $lastWorkout ?? 'Nunca treinou',
                    'risk_level' => 'Alto'
                ];
            });

        return [
            'churn_risk' => $churnRisk->values()->toArray(),
            'total_at_risk' => $churnRisk->count(),
        ];
    }

    public static function companyPerformance(int $companyId): array
    {
        // 1. Buscar todos os profissionais da mesma empresa/unidade
        $professionals = User::where('academy_company_id', $companyId)
            ->whereHas('roles', fn($q) => $q->where('name', 'professional'))
            ->get();

        if ($professionals->isEmpty()) {
            return ['professionals' => [], 'total_company_patients' => 0];
        }

        // 2. Agregar estatísticas por profissional
        $stats = $professionals->map(function($prof) {
            $patientsCount = DB::table('pacientes')
                ->where('profissional_id', $prof->id)
                ->where('status', 'Sim')
                ->count();
            
            // Atividade recente dos alunos deste profissional (últimos 7 dias)
            $studentIds = DB::table('pacientes')
                ->where('profissional_id', $prof->id)
                ->where('status', 'Sim')
                ->pluck('user_id');

            $recentActivity = DB::table('exercise_entries')
                ->whereIn('user_id', $studentIds)
                ->where('entry_date', '>=', now()->subDays(7)->toDateString())
                ->count();

            return [
                'id' => $prof->id,
                'name' => $prof->name,
                'email' => $prof->email,
                'avatar' => $prof->avatar,
                'patients_count' => $patientsCount,
                'recent_activity_score' => $patientsCount > 0 ? round(($recentActivity / ($patientsCount * 3)) * 100) : 0, // Score baseado em média de 3 treinos/semana
            ];
        });

        return [
            'professionals' => $stats->sortByDesc('patients_count')->values()->toArray(),
            'total_company_patients' => $stats->sum('patients_count'),
        ];
    }
}
