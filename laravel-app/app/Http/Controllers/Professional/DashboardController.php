<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard profissional (Para Nutrólogos, Nutricionistas e Personals).
     */
    public function index(Request $request): View
    {
        $uid = auth()->id();
        $professional = auth()->user();
        $professional->generateProfessionalCode(); 

        // 1. Métricas Principais
        $patientsQuery = $professional->patients();
        $totalPatients = $patientsQuery->count();
        
        // Ativos: Logaram ou registraram algo nos últimos 7 dias
        $activePatientsCount = $professional->patients()
            ->where('last_activity_at', '>=', now()->subDays(7))
            ->count();
        
        $inactivePatientsCount = $totalPatients - $activePatientsCount;
        
        $newPatientsMonth = $professional->patients()
            ->wherePivot('created_at', '>=', now()->startOfMonth())
            ->count();

        $pendingAssessmentsCount = \App\Models\BodyAssessment::where('professional_id', $uid)
            ->where('status', 'pending')
            ->count();

        $activeWorkoutsCount = \App\Models\TrainingPlan::where('is_active', true)
            ->count();

        // Dietas ativas (MealTemplates vinculados ao profissional para seus alunos)
        $activeDietsCount = \App\Models\MealTemplate::where('user_id', $uid)->count();

        // 2. Agenda
        $todayAppointments = \App\Models\ProfessionalAppointment::with('patient')
            ->where('professional_id', $uid)
            ->whereDate('appointment_at', now()->toDateString())
            ->orderBy('appointment_at')
            ->get();

        $nextAppointments = \App\Models\ProfessionalAppointment::with('patient')
            ->where('professional_id', $uid)
            ->where('appointment_at', '>', now())
            ->orderBy('appointment_at')
            ->limit(5)
            ->get();

        // Aniversariantes do dia
        $birthdayPatients = $professional->patients()
            ->whereHas('profile', function ($q) {
                $q->whereMonth('birth_date', now()->month)
                    ->whereDay('birth_date', now()->day);
            })
            ->get();

        // 3. Alertas Inteligentes (NexSense)
        $tasks = [];
        
        // Alerta: Registro Profissional Vencendo
        if ($professional->professionalProfile) {
            $daysExpiry = $professional->professionalProfile->daysUntilExpiry();
            if ($daysExpiry !== null && $daysExpiry <= 30) {
                $tasks[] = [
                    'id' => 'expiry_alert',
                    'type' => 'security',
                    'msg' => $professional->professionalProfile->expiry_warning,
                    'priority' => $daysExpiry <= 7 ? 'critical' : 'high'
                ];
            }
        }

        // Alerta: Pacientes sem avaliação recente (> 30 dias)
        $lateAssessments = $professional->patients()
            ->whereDoesntHave('assessments', function($q) {
                $q->where('assessment_date', '>=', now()->subDays(30));
            })->limit(3)->get();

        foreach($lateAssessments as $u) {
            $tasks[] = [
                'id' => uniqid(),
                'type' => 'assessment',
                'msg' => 'Paciente ' . explode(' ', $u->name)[0] . ' está sem avaliação há mais de 30 dias.',
                'priority' => 'medium'
            ];
        }

        // Alerta: Paciente Inativo (sem logs há 3 dias)
        $inactiveAlerts = $professional->patients()
            ->where('last_activity_at', '<', now()->subDays(3))
            ->where('last_activity_at', '>=', now()->subDays(10))
            ->limit(2)
            ->get();
        
        foreach($inactiveAlerts as $ia) {
            $tasks[] = [
                'id' => uniqid(),
                'type' => 'engagement',
                'msg' => explode(' ', $ia->name)[0] . ' não registra atividades há 3 dias. Envie um incentivo!',
                'priority' => 'high'
            ];
        }

        // 4. Seção de Pacientes Recentes (Aderência)
        $recentPatients = $professional->patients()->with(['profile'])
            ->withCount(['foodEntries' => function($q) {
                $q->where('entry_date', '>=', now()->subDays(7));
            }])
            ->orderBy('users.id', 'desc')
            ->limit(5)
            ->get()
            ->map(function($user) {
                $logsCount = $user->food_entries_count;
                $engagement = min(100, $logsCount * 7.14); // 2 logs por dia = 100% aprox
                
                $nameParts = explode(' ', $user->name);
                $initials = collect($nameParts)->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'bio' => ($user->profile?->goal ?? 'Performance') . ' • ' . ($user->profile?->sex ?? 'Bio'),
                    'status' => $engagement > 70 ? 'Excelente' : ($engagement > 40 ? 'Regular' : 'Inativo'),
                    'engage' => (int)$engagement,
                    'initials' => strtoupper($initials),
                    'color' => $engagement > 75 ? 'from-emerald-500 to-teal-500' : ($engagement > 35 ? 'from-amber-500 to-orange-500' : 'from-rose-500 to-red-600')
                ];
            });

        $lastMonthPatients = $professional->patients()
            ->wherePivot('created_at', '<', now()->startOfMonth())
            ->wherePivot('created_at', '>=', now()->subMonth()->startOfMonth())
            ->count();

        $growth = $lastMonthPatients > 0 
            ? (($newPatientsMonth) / $lastMonthPatients) * 100 
            : 100;

        // Cálculo de faturamento dinâmico (Simulado: R$ 150 por paciente ativo)
        $revenuePerPatient = 150;
        $stats = [
            'total_patients' => $totalPatients,
            'active_patients' => $activePatientsCount,
            'inactive_patients' => $inactivePatientsCount,
            'new_patients' => $newPatientsMonth,
            'growth' => round($growth, 1),
            'pending_assessments' => $pendingAssessmentsCount,
            'active_workouts' => $activeWorkoutsCount,
            'active_diets' => $activeDietsCount,
            'revenue_month' => 'R$ ' . number_format($activePatientsCount * $revenuePerPatient, 2, ',', '.'), 
            'projected_revenue' => 'R$ ' . number_format($totalPatients * $revenuePerPatient, 2, ',', '.'),
        ];

        // 5. Dados de Engajamento para Gráfico (Últimos 7 dias)
        $engagementData = [];
        $engagementLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $engagementLabels[] = $date->translatedFormat('D');
            
            // Contar quantos pacientes logaram nesse dia (FoodEntry, por exemplo)
            if ($totalPatients > 0) {
                // Simplificado: usar last_activity_at ou registros reais se disponíveis
                // Para este exemplo, vamos simular uma curva baseada na média real
                $dayCount = \App\Models\FoodEntry::whereIn('user_id', $professional->patients()->pluck('users.id'))
                    ->whereDate('entry_date', $date->toDateString())
                    ->distinct('user_id')
                    ->count();
                
                $percent = ($dayCount / $totalPatients) * 100;
                $engagementData[] = (int)min(100, $percent + ($activePatientsCount / max(1, $totalPatients)) * 20); // Ajuste para gráfico não ficar vazio
            } else {
                $engagementData[] = 0;
            }
        }

        $professionalCode = $professional->professional_code;
        $qrCodeUrl = $professional->getProfessionalQrCodeUrl();

        return view('professional.dashboard', compact(
            'stats', 
            'tasks', 
            'engagementData', 
            'engagementLabels',
            'recentPatients', 
            'professionalCode', 
            'qrCodeUrl',
            'todayAppointments',
            'nextAppointments',
            'birthdayPatients'
        ));
    }
}
