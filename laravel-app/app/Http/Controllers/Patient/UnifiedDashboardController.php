<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use App\Models\PatientDocument;
use App\Models\ProfessionalAppointment;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\WeightEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnifiedDashboardController extends Controller
{
    /**
     * Exibe a tela de escolha entre visão unificada ou profissional específico.
     */
    public function choice()
    {
        $patient = Auth::user();
        $professionals = $patient->professionals()->wherePivot('status', 'Sim')->get();

        if ($professionals->count() <= 1) {
            return redirect()->route('patient.portal');
        }

        return view('patient.dashboard_choice', compact('professionals'));
    }

    /**
     * Painel Unificado do Paciente.
     */
    public function index(Request $request)
    {
        $patient = Auth::user();
        $profile = $patient->profile;
        
        // 1. Profissionais Vinculados
        $professionals = $patient->professionals()
            ->with(['branding'])
            ->wherePivot('status', 'Sim')
            ->get();

        $professionalIds = $professionals->pluck('id');
        
        // Identificar Profissional Selecionado (ou Geral)
        $selectedProfessionalId = $request->get('professional_id', session('active_professional_id'));
        $activeProfessional = $professionals->where('id', $selectedProfessionalId)->first();
        
        // Se houver apenas 1 profissional e nenhum selecionado, seleciona ele automaticamente? 
        // Não, vamos manter a visão "Geral" como padrão se houver mais de um.
        
        // 2. Resumo Geral
        $lastWeight = WeightEntry::where('user_id', $patient->id)->latest('weighed_at')->first();
        $age = $profile && $profile->birth_date ? $profile->birth_date->age : null;
        
        $summary = [
            'name' => $patient->name,
            'age' => $age,
            'weight' => $lastWeight ? $lastWeight->weight_kg : ($profile->weight_kg ?? null),
            'height' => $profile ? $profile->height_cm : null,
            'goal' => $profile ? $profile->goal : null,
            'last_update' => $patient->updated_at,
            'status' => $patient->status === 'active' ? 'Ativo' : 'Pendente',
            'health_score' => $patient->health_score ?? 85,
            'profile_type' => $patient->hasRole('aluno') ? 'Aluno + Paciente' : 'Paciente',
        ];

        // 3. Agenda Unificada (ou filtrada)
        $appointmentsQuery = ProfessionalAppointment::where('patient_id', $patient->id)
            ->where('appointment_at', '>=', now()->subDays(1));
        
        if ($activeProfessional) {
            $appointmentsQuery->where('professional_id', $activeProfessional->id);
        } else {
            $appointmentsQuery->whereIn('professional_id', $professionalIds);
        }

        $appointments = $appointmentsQuery->orderBy('appointment_at', 'asc')->get();

        // 4. Dados Específicos do Profissional (Se selecionado)
        $treatmentPlan = null;
        $prescriptions = collect();
        $evolutions = collect();

        if ($activeProfessional) {
            $treatmentPlan = $patient->treatmentPlans()
                ->where('professional_id', $activeProfessional->id)
                ->where('is_active', true)
                ->first();

            $prescriptions = $patient->medicalPrescriptions()
                ->where('professional_id', $activeProfessional->id)
                ->latest()
                ->take(5)
                ->get();

            $evolutions = $patient->medicalEvolutions()
                ->where('professional_id', $activeProfessional->id)
                ->latest('date')
                ->take(3)
                ->get();
        }

        // 5. Alertas e Pendências
        $unreadMessages = \App\Models\InternalEmail::where('recipient_id', $patient->id)
            ->where('is_read', false)
            ->count();

        $alerts = [
            'workouts' => TrainingPlan::where('user_id', $patient->id)->where('is_active', true)->count(),
            'assessments' => BodyAssessment::where('user_id', $patient->id)->where('created_at', '>=', now()->subDays(30))->count(),
            'appointments' => $appointments->where('appointment_at', '>=', now())->count(),
            'messages' => $unreadMessages,
            'documents' => PatientDocument::where('patient_id', $patient->id)->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // 6. Recuperação e Descanso Ativo (Biohacking)
        $lastWorkout = \App\Models\WorkoutSession::where('user_id', $patient->id)->latest()->first();
        
        // Contagem de protocolos na semana
        $recoveryCountThisWeek = \App\Models\ActiveRestLog::where('user_id', $patient->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();
        
        // Sugestão de Recuperação Baseada na Carga/Tipo de Treino
        // Por agora, sugerimos uma rotina premium se for Pro, ou uma básica.
        $recommendedRoutine = null;
        if ($lastWorkout) {
            // Se o treino foi pesado (RPE > 8), sugerimos alongamento profundo
            if ($lastWorkout->rpe_score >= 8) {
                $recommendedRoutine = \App\Models\ActiveRestRoutine::where('category', 'Alongamento')->first();
            } else {
                $recommendedRoutine = \App\Models\ActiveRestRoutine::where('category', 'Mobilidade')->first();
            }
        }
        
        // Se não houver sugestão específica, pega um favorito ou o primeiro ativo
        if (!$recommendedRoutine) {
            $favorite = \App\Models\ActiveRestFavorite::where('user_id', $patient->id)->first();
            if ($favorite) {
                $recommendedRoutine = $favorite->routine;
            } else {
                $recommendedRoutine = \App\Models\ActiveRestRoutine::where('is_active', true)->orderBy('order')->first();
            }
        }

        // 7. Documentos e Laudos (Listagem)
        $documents = PatientDocument::where('patient_id', $patient->id)
            ->with('professional')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($doc) {
                return $doc->professional->name;
            });

        // 8. Branding dinâmico (Usa o do profissional se selecionado, senão usa um padrão NexShape)
        $branding = $activeProfessional && $activeProfessional->branding 
            ? $activeProfessional->branding->toArray() 
            : [
                'primary_color' => '#6366f1',
                'accent_color' => '#a855f7',
                'primary_color_dim' => '#6366f120',
                'clinic_name' => 'NexShape',
                'logo' => null
            ];

        return view('patient.unified_dashboard', compact(
            'patient',
            'summary',
            'professionals',
            'activeProfessional',
            'appointments',
            'alerts',
            'treatmentPlan',
            'prescriptions',
            'evolutions',
            'branding',
            'documents',
            'recommendedRoutine',
            'lastWorkout',
            'recoveryCountThisWeek'
        ));
    }
}
