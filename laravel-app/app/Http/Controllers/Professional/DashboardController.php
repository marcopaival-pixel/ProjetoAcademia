<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use App\Models\HealthAlert;
use App\Models\ProfessionalAppointment;
use App\Models\Subscription;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Support\PatientAccessGuard;
use Illuminate\Http\Request;
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
        /** @var \App\Models\ProfessionalProfile|null $profile */
        $profile = $professional->professionalProfile;
        $professionName = $profile?->profession?->getAttribute('name') ?: 'Geral';
        
        $patientLabel = __t('Paciente');
        $patientsLabel = $patientLabel === 'Aluno' ? 'Alunos' : ($patientLabel === 'Cliente' ? 'Clientes' : 'Pacientes');

        // Check if there is an active patient
        $activePatient = null;
        if (session()->has('active_patient_id')) {
            $activePatientId = PatientAccessGuard::resolveActivePatientId($professional);
            $activePatient = $activePatientId ? User::find($activePatientId) : null;
        }

        // 1. MEUS ALUNOS / PACIENTES GLOBAIS
        $patientsQuery = $professional->patients();
        $totalPatients = $patientsQuery->count();
        
        $activePatientsCount = $professional->patients()
            ->where('last_activity_at', '>=', now()->subDays(30))
            ->count();
        
        $inactivePatientsCount = $totalPatients - $activePatientsCount;
        
        $newPatientsMonth = $professional->patients()
            ->wherePivot('created_at', '>=', now()->startOfMonth())
            ->count();

        $birthdayPatientsCount = $professional->patients()
            ->whereHas('profile', function ($q) {
                $q->whereMonth('birth_date', now()->month);
            })
            ->count();

        // 2. INDICADORES OPERACIONAIS GERAIS
        $activeWorkoutsCount = TrainingPlan::where('professional_id', $uid)
            ->where('is_active', true)
            ->count();

        $assessmentsMonthCount = BodyAssessment::where('professional_id', $uid)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $appointmentsMonthCount = ProfessionalAppointment::where('professional_id', $uid)
            ->whereMonth('appointment_at', now()->month)
            ->whereYear('appointment_at', now()->year)
            ->count();
            
        $appointmentsCompletedMonth = ProfessionalAppointment::where('professional_id', $uid)
            ->whereMonth('appointment_at', now()->month)
            ->whereYear('appointment_at', now()->year)
            ->where('appointment_at', '<', now())
            ->count();

        $revenueMonth = null;

        // 3. AGENDA DE HOJE GERAL
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todayAppointments = ProfessionalAppointment::with('patient')
            ->where('professional_id', $uid)
            ->whereBetween('appointment_at', [$todayStart, $todayEnd])
            ->orderBy('appointment_at')
            ->get();

        // 4. PENDÊNCIAS GERAIS
        $pendingAssessmentsCount = BodyAssessment::where('professional_id', $uid)
            ->where('status', 'pending')
            ->count();

        $expiredTrainingsCount = TrainingPlan::where('professional_id', $uid)
            ->where('is_active', true)
            ->where('created_at', '<', now()->subDays(45))
            ->count();

        $pendingAppointmentsCount = ProfessionalAppointment::where('professional_id', $uid)
            ->where('appointment_at', '>=', $todayStart)
            ->where('status', 'pending')
            ->count();

        $inactiveOver30Days = $inactivePatientsCount;
        $pendingDocumentsCount = 0;
        $unreadMessagesCount = HealthAlert::whereIn('user_id', $professional->patients()->pluck('users.id'))
            ->where('is_read', false)->count();

        // 5. ATIVIDADE RECENTE
        $recentWorkouts = TrainingPlan::where('professional_id', $uid)
            ->with('user:id,name')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn ($plan) => [
                'icon' => 'dumbbell',
                'text' => 'Treino criado para '.($plan->user?->name ?? 'paciente'),
                'time' => $plan->created_at?->diffForHumans() ?? '',
                'color' => 'emerald',
            ]);

        $recentAssessments = BodyAssessment::where('professional_id', $uid)
            ->with('user:id,name')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn ($assessment) => [
                'icon' => 'clipboard-check',
                'text' => 'Avaliação registrada para '.($assessment->user?->name ?? 'paciente'),
                'time' => $assessment->created_at?->diffForHumans() ?? '',
                'color' => 'blue',
            ]);

        $recentActivities = $recentWorkouts
            ->concat($recentAssessments)
            ->take(5)
            ->values();

        // 6. ÚLTIMOS ACESSADOS
        $recentPatients = $professional->patients()->with(['profile'])
            ->orderBy('users.id', 'desc')
            ->limit(5)
            ->get()
            ->map(function($user) {
                $nameParts = explode(' ', $user->name);
                $initials = collect($nameParts)->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initials' => strtoupper($initials),
                    'color' => 'from-zinc-700 to-zinc-900',
                ];
            });

        // 7. DICAS INTELIGENTES
        $smartTips = [];
        $lateAssessments = $professional->patients()
            ->whereDoesntHave('assessments', function($q) {
                $q->where('assessment_date', '>=', now()->subDays(90));
            })->count();

        if ($lateAssessments > 0) {
            $smartTips[] = "{$lateAssessments} {$patientsLabel} estão sem avaliação há mais de 90 dias.";
        }
        if ($expiredTrainingsCount > 0) {
            $smartTips[] = "{$expiredTrainingsCount} {$patientsLabel} possuem treinos próximos do vencimento ou vencidos.";
        }
        if ($pendingAppointmentsCount > 0) {
            $smartTips[] = "{$pendingAppointmentsCount} consultas precisam de confirmação.";
        }
        if (empty($smartTips)) {
            $smartTips[] = "Tudo em dia! Nenhuma ação corretiva crítica necessária.";
        }

        // 8. ATALHOS RÁPIDOS
        $quickShortcuts = [
            ['label' => 'Novo ' . $patientLabel, 'icon' => 'user-plus', 'route' => route('professional.patients.create'), 'color' => 'blue'],
            ['label' => 'Novo Treino', 'icon' => 'dumbbell', 'route' => route('professional.patients.index'), 'color' => 'emerald'],
            ['label' => 'Nova Avaliação', 'icon' => 'clipboard-list', 'route' => route('professional.patients.index'), 'color' => 'purple'],
            ['label' => 'Nova Consulta', 'icon' => 'calendar-plus', 'route' => route('agenda.index'), 'color' => 'amber'],
            ['label' => 'Nova Cobrança', 'icon' => 'dollar-sign', 'route' => route('professional.finance.dashboard'), 'color' => 'emerald'],
            ['label' => 'Importar Treino IA', 'icon' => 'sparkles', 'route' => route('progression.plans.import-photo'), 'color' => 'indigo'],
            ['label' => 'Novo Atendimento', 'icon' => 'stethoscope', 'route' => route('professional.patients.index'), 'color' => 'rose'],
            ['label' => 'Novo Arquivo', 'icon' => 'file-plus', 'route' => route('professional.patients.index'), 'color' => 'zinc'],
        ];

        // 9. DADOS DO PACIENTE ATIVO (Se aplicável)
        $activePatientStats = [];
        if ($activePatient) {
            $lastAssessment = BodyAssessment::where('user_id', $activePatient->id)
                ->where('professional_id', $uid)
                ->orderBy('created_at', 'desc')
                ->first();
            $nextAppointment = ProfessionalAppointment::where('patient_id', $activePatient->id)
                ->where('professional_id', $uid)
                ->where('appointment_at', '>', now())
                ->orderBy('appointment_at', 'asc')
                ->first();
            $lastTraining = TrainingPlan::where('user_id', $activePatient->id)
                ->where('professional_id', $uid)
                ->orderBy('created_at', 'desc')
                ->first();
            $subscription = Subscription::with('plan')
                ->where('user_id', $activePatient->id)
                ->latest()
                ->first();
            
            $activePatientStats = [
                'last_assessment' => $lastAssessment ? $lastAssessment->created_at->format('d/m/Y') : 'Nenhuma',
                'next_appointment' => $nextAppointment ? \Carbon\Carbon::parse($nextAppointment->appointment_at)->format('d/m/Y H:i') : 'Não agendada',
                'last_training' => $lastTraining ? $lastTraining->created_at->format('d/m/Y') : 'Nenhum',
                'status' => $activePatient->last_activity_at && $activePatient->last_activity_at > now()->subDays(30) ? 'Ativo' : 'Inativo',
                'active_plan' => $subscription?->plan?->name ?? 'Sem plano ativo',
            ];
        }

        return view('professional.dashboard', compact(
            'professional',
            'professionName',
            'patientLabel',
            'patientsLabel',
            'activePatient',
            'activePatientStats',
            'totalPatients',
            'activePatientsCount',
            'inactivePatientsCount',
            'newPatientsMonth',
            'birthdayPatientsCount',
            'activeWorkoutsCount',
            'assessmentsMonthCount',
            'appointmentsMonthCount',
            'appointmentsCompletedMonth',
            'revenueMonth',
            'todayAppointments',
            'pendingAssessmentsCount',
            'expiredTrainingsCount',
            'pendingAppointmentsCount',
            'inactiveOver30Days',
            'pendingDocumentsCount',
            'unreadMessagesCount',
            'recentActivities',
            'recentPatients',
            'smartTips',
            'quickShortcuts'
        ));
    }
}
