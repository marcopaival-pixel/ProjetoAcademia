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
        $profile = $professional->professionalProfile;
        $professionName = $profile && $profile->profession ? $profile->profession->name : 'Geral';
        
        $patientLabel = __t('Paciente');
        $patientsLabel = $patientLabel === 'Aluno' ? 'Alunos' : ($patientLabel === 'Cliente' ? 'Clientes' : 'Pacientes');

        // Check if there is an active patient
        $activePatient = null;
        if (session()->has('active_patient_id')) {
            $activePatient = \App\Models\User::find(session('active_patient_id'));
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
        $activeWorkoutsCount = \App\Models\TrainingPlan::where('professional_id', $uid)
            ->where('is_active', true)
            ->count();

        $assessmentsMonthCount = \App\Models\BodyAssessment::where('professional_id', $uid)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $appointmentsMonthCount = \App\Models\ProfessionalAppointment::where('professional_id', $uid)
            ->whereMonth('appointment_at', now()->month)
            ->whereYear('appointment_at', now()->year)
            ->count();
            
        $appointmentsCompletedMonth = \App\Models\ProfessionalAppointment::where('professional_id', $uid)
            ->whereMonth('appointment_at', now()->month)
            ->whereYear('appointment_at', now()->year)
            ->where('appointment_at', '<', now())
            ->count();

        $revenuePerPatient = 150;
        $revenueMonth = $activePatientsCount * $revenuePerPatient; // Simulação

        // 3. AGENDA DE HOJE GERAL
        $todayAppointments = \App\Models\ProfessionalAppointment::with('patient')
            ->where('professional_id', $uid)
            ->whereDate('appointment_at', now()->toDateString())
            ->orderBy('appointment_at')
            ->get();

        // 4. PENDÊNCIAS GERAIS
        $pendingAssessmentsCount = \App\Models\BodyAssessment::where('professional_id', $uid)
            ->where('status', 'pending')
            ->count();

        $expiredTrainingsCount = \App\Models\TrainingPlan::where('professional_id', $uid)
            ->where('is_active', true)
            ->where('created_at', '<', now()->subDays(45))
            ->count();

        $pendingAppointmentsCount = \App\Models\ProfessionalAppointment::where('professional_id', $uid)
            ->whereDate('appointment_at', '>=', now()->toDateString())
            ->where('status', 'pending')
            ->count();

        $inactiveOver30Days = $inactivePatientsCount;
        $pendingDocumentsCount = 0;
        $unreadMessagesCount = \App\Models\HealthAlert::whereIn('user_id', $professional->patients()->pluck('users.id'))
            ->where('is_read', false)->count();

        // 5. ATIVIDADE RECENTE
        $recentActivities = [
            ['icon' => 'dumbbell', 'text' => 'Treino criado para João Silva', 'time' => 'Há 2 horas', 'color' => 'emerald'],
            ['icon' => 'clipboard-check', 'text' => 'Avaliação de Maria Souza concluída', 'time' => 'Há 4 horas', 'color' => 'blue'],
            ['icon' => 'video', 'text' => 'Consulta com Pedro Alves finalizada', 'time' => 'Há 5 horas', 'color' => 'purple'],
            ['icon' => 'file-text', 'text' => 'Plano alimentar enviado para Ana Costa', 'time' => 'Ontem', 'color' => 'amber'],
        ];

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
            $lastAssessment = \App\Models\BodyAssessment::where('user_id', $activePatient->id)->orderBy('created_at', 'desc')->first();
            $nextAppointment = \App\Models\ProfessionalAppointment::where('patient_id', $activePatient->id)
                ->where('appointment_at', '>', now())
                ->orderBy('appointment_at', 'asc')
                ->first();
            $lastTraining = \App\Models\TrainingPlan::where('user_id', $activePatient->id)->orderBy('created_at', 'desc')->first();
            
            $activePatientStats = [
                'last_assessment' => $lastAssessment ? $lastAssessment->created_at->format('d/m/Y') : 'Nenhuma',
                'next_appointment' => $nextAppointment ? \Carbon\Carbon::parse($nextAppointment->appointment_at)->format('d/m/Y H:i') : 'Não agendada',
                'last_training' => $lastTraining ? $lastTraining->created_at->format('d/m/Y') : 'Nenhum',
                'status' => $activePatient->last_activity_at && $activePatient->last_activity_at > now()->subDays(30) ? 'Ativo' : 'Inativo',
                'active_plan' => $activePatient->subscription_status === 'active' ? 'Plano Premium' : 'Plano Básico', // Mocking plan status
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



