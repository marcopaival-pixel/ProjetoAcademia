<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\PatientAccessToken;
use App\Models\AdminLog;
use App\Models\ProfessionalPatient;
use App\Models\PatientTreatmentPlan;
use App\Models\PatientDocument;
use App\Models\BodyAssessment;
use App\Models\ProfessionalAppointment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    /**
     * Valida o token de acesso e autentica o paciente.
     * Rota: /patient/access?token=xxx
     */
    public function access(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Token de acesso não fornecido.');
        }

        $accessToken = PatientAccessToken::where('token_hash', hash('sha256', $token))
            ->where('status', 'active')
            ->first();

        if (!$accessToken || !$accessToken->isValid()) {
            return redirect()->route('login')->with('error', 'Token de acesso inválido ou expirado.');
        }

        $patient = $accessToken->patient;
        
        if (!$patient) {
            return redirect()->route('login')->with('error', 'Paciente não encontrado.');
        }

        auth()->login($patient);

        $accessToken->update([
            'used_at' => now(),
            'status' => 'used' 
        ]);

        AdminLog::create([
            'user_id' => $patient->id,
            'action' => 'patient_portal_token_access',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => ['token_id' => $accessToken->id],
            'created_at' => now()
        ]);

        if (!$patient->perfil_paciente_completo) {
            return redirect()->route('patient.profile.complete')->with('success', 'Bem-vindo! Por favor, complete seu cadastro.');
        }

        return redirect()->route('patient.portal')->with('success', 'Acesso autorizado com sucesso!');
    }

    /**
     * Dashboard Principal (Resumo)
     */
    public function index(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);

        if (!$context['primaryLink']) {
            return view('patient.no-link');
        }

        $activeProfId = session('active_professional_id');

        // Summary Data for Dashboard
        $summary = [
            'professional' => $context['professional'],
            'last_appointment' => ProfessionalAppointment::where('patient_id', $patient->id)
                ->where('professional_id', $activeProfId)
                ->where('appointment_at', '<', now())
                ->latest('appointment_at')
                ->first(),
            'next_appointment' => ProfessionalAppointment::where('patient_id', $patient->id)
                ->where('professional_id', $activeProfId)
                ->where('appointment_at', '>=', now())
                ->orderBy('appointment_at', 'asc')
                ->first(),
            'tracking_status' => $context['primaryLink']->tracking_status ?? 'Em andamento',
        ];

        return view('patient.dashboard', array_merge($context, ['summary' => $summary]));
    }

    /**
     * Plano de Tratamento
     */
    public function treatmentPlan(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        $activeProfId = session('active_professional_id') ?? ($context['primaryLink']->profissional_id ?? null);
        
        $plan = PatientTreatmentPlan::where('patient_id', $patient->id)
            ->where('professional_id', $activeProfId)
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('patient.treatment-plan', array_merge($context, ['plan' => $plan]));
    }

    /**
     * Evolução do Paciente
     */
    public function evolution(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        $activeProfId = session('active_professional_id') ?? ($context['primaryLink']->profissional_id ?? null);
        
        $assessments = BodyAssessment::where('user_id', $patient->id)
            ->where('professional_id', $activeProfId)
            ->orderBy('assessment_date', 'asc')
            ->get();

        $processedAssessments = collect();
        $chartData = [
            'dates' => [],
            'weight' => [],
            'bf' => [],
        ];

        foreach ($assessments as $index => $assessment) {
            $prev = $assessments->get($index - 1);
            $assessment->delta_weight = $prev ? $assessment->weight_kg - $prev->weight_kg : 0;
            $assessment->delta_bf = $prev ? $assessment->bf_percent - $prev->bf_percent : 0;
            $processedAssessments->push($assessment);
            
            $chartData['dates'][] = $assessment->assessment_date->format('d/m/y');
            $chartData['weight'][] = (float) $assessment->weight_kg;
            $chartData['bf'][] = (float) $assessment->bf_percent;
        }

        // Fotos de Evolução (Análise Corporal)
        $photos = \App\Models\BodyAnalysis::where('user_id', $patient->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('view_type');

        $evolutionPhotos = [];
        foreach (['front', 'side', 'back'] as $type) {
            if (isset($photos[$type]) && $photos[$type]->count() >= 2) {
                $evolutionPhotos[$type] = [
                    'first' => $photos[$type]->first(),
                    'last' => $photos[$type]->last(),
                ];
            }
        }

        return view('patient.evolution', array_merge($context, [
            'assessments' => $processedAssessments->reverse(),
            'latest' => $processedAssessments->last(),
            'chartData' => $chartData,
            'evolutionPhotos' => $evolutionPhotos,
            'gender' => $patient->genero ?? 'M' // Assume Masculino se não definido
        ]));
    }

    /**
     * Prescrições (Treinos e Dietas)
     */
    public function prescriptions(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        $activeProfId = session('active_professional_id');
        $trainings = $patient->trainingPlans()->where('professional_id', $activeProfId)->where('is_active', true)->get();
        $diets = $patient->mealTemplates()->where('professional_id', $activeProfId)->latest()->get();

        return view('patient.prescriptions', array_merge($context, [
            'trainings' => $trainings,
            'diets' => $diets
        ]));
    }

    /**
     * Documentos (Exames, Receitas, etc)
     */
    public function documents(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        $documents = PatientDocument::where('patient_id', $patient->id)
            ->where('professional_id', session('active_professional_id'))
            ->latest()
            ->get();

        return view('patient.documents', array_merge($context, ['documents' => $documents]));
    }

    /**
     * Agenda
     */
    public function agenda(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        $appointments = ProfessionalAppointment::where('patient_id', $patient->id)
            ->with('professional')
            ->orderBy('appointment_at', 'desc')
            ->get();

        return view('patient.agenda', array_merge($context, ['appointments' => $appointments]));
    }

    /**
     * Prontuário Médico (Portal do Paciente)
     */
    public function medicalRecords(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        $activeProfId = session('active_professional_id');
        
        $evolutions = $patient->medicalEvolutions()->where('professional_id', $activeProfId)->latest('date')->limit(5)->get();
        $reports = $patient->medicalReports()->where('professional_id', $activeProfId)->latest('date')->limit(5)->get();
        $prescriptions = $patient->medicalPrescriptions()->where('professional_id', $activeProfId)->latest('date')->limit(5)->get();
        $certificates = $patient->medicalCertificates()->where('professional_id', $activeProfId)->latest('date')->limit(5)->get();

        return view('patient.medical-records.index', array_merge($context, [
            'evolutions' => $evolutions,
            'reports' => $reports,
            'prescriptions' => $prescriptions,
            'certificates' => $certificates,
        ]));
    }

    public function medicalEvolutions(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        $evolutions = $patient->medicalEvolutions()
            ->where('professional_id', session('active_professional_id'))
            ->latest('date')
            ->paginate(10);

        return view('patient.medical-records.evolutions', array_merge($context, ['evolutions' => $evolutions]));
    }

    public function medicalReports(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        $reports = $patient->medicalReports()
            ->where('professional_id', session('active_professional_id'))
            ->latest('date')
            ->paginate(10);

        return view('patient.medical-records.reports', array_merge($context, ['reports' => $reports]));
    }

    public function medicalPrescriptions(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        $prescriptions = $patient->medicalPrescriptions()
            ->where('professional_id', session('active_professional_id'))
            ->latest('date')
            ->paginate(10);

        return view('patient.medical-records.prescriptions', array_merge($context, ['prescriptions' => $prescriptions]));
    }

    public function medicalCertificates(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        $certificates = $patient->medicalCertificates()
            ->where('professional_id', session('active_professional_id'))
            ->latest('date')
            ->paginate(10);

        return view('patient.medical-records.certificates', array_merge($context, ['certificates' => $certificates]));
    }

    /**
     * Exibe os planos disponíveis para o paciente se tornar aluno.
     */
    public function plans(): View
    {
        $user = Auth::user();

        // Se já for aluno, redireciona para a página de assinatura padrão ou dashboard
        if ($user->hasRole('aluno')) {
            return redirect()->route('patient.subscription.index');
        }

        $context = $this->getPatientContext($user);

        // Planos formatados para exibição (Simulação baseada no banco + desconto exclusivo)
        $plans = [
            [
                'id' => 2,
                'name' => 'Performance Elite',
                'description' => 'Acesso total aos treinos, dieta e evolução IA.',
                'original_price' => 29.90,
                'patient_price' => 14.90,
                'savings' => 15.00,
                'recommended' => true,
                'features' => [
                    'Treinos personalizados pelo seu profissional',
                    'Acesso ao Chat IA Ilimitado',
                    'Registro de cargas e evolução de força',
                    'Diário alimentar com análise de macros',
                    'Galeria de fotos e evolução corporal',
                ]
            ]
        ];

        return view('patient.plans', array_merge($context, [
            'availablePlans' => $plans
        ]));
    }

    public function downloadReport(\App\Models\MedicalReport $report, \App\Services\DompdfPdfService $pdfService)
    {
        $patient = Auth::user();
        if ($report->user_id !== $patient->id) abort(403);
        
        $html = view('professional.medical-records.reports.pdf', compact('patient', 'report'))->render();
        return $pdfService->generate($html, "laudo-{$report->id}.pdf");
    }

    public function downloadCertificate(\App\Models\MedicalCertificate $certificate, \App\Services\DompdfPdfService $pdfService)
    {
        $patient = Auth::user();
        if ($certificate->user_id !== $patient->id) abort(403);
        
        $html = view('professional.medical-records.certificates.pdf', compact('patient', 'certificate'))->render();
        return $pdfService->generate($html, "atestado-{$certificate->id}.pdf");
    }

    /**
     * Mensagens e Avisos
     */
    public function messages(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);
        
        // Avisos do profissional guardados no link
        $alerts = $context['primaryLink']->professional_notes_for_patient ?? null;

        return view('patient.messages', array_merge($context, ['alerts' => $alerts]));
    }

    /**
     * Exportar Laudo Próprio (Item 12)
     */
    public function exportLaudo()
    {
        $patient = Auth::user();
        // Reutiliza o controller de exportação de laudo do profissional
        return app(\App\Http\Controllers\Professional\PatientReportController::class)->export($patient, app(\App\Services\DompdfPdfService::class));
    }

    /**
     * Histórico de Acessos (Item 12 e 13)
     */
    public function accessLogs(): View
    {
        $patient = Auth::user();
        $context = $this->getPatientContext($patient);

        $logs = AdminLog::where('payload->model_id', $patient->id)
            ->where('payload->model_class', \App\Models\User::class)
            ->latest()
            ->paginate(15);

        return view('patient.access-logs', array_merge($context, ['logs' => $logs]));
    }

    /**
     * Helper para buscar o contexto do paciente (Branding, Professional, Link)
     */
    private function getPatientContext($patient)
    {
        $links = ProfessionalPatient::where('user_id', $patient->id)
            ->where('status', 'Sim')
            ->with(['professional.branding'])
            ->latest()
            ->get();

        $activeProfId = session('active_professional_id');
        $primaryLink = $links->firstWhere('profissional_id', $activeProfId) ?? $links->first();
        
        $professional = $primaryLink?->professional;
        $brandingData = $professional?->branding;
        $permissions = $primaryLink?->patient_permissions ?? [];

        $branding = [
            'clinic_name' => $brandingData->clinic_name ?? ($professional->name ?? 'Portal do Paciente'),
            'primary_color' => $brandingData->primary_color ?? '#3b82f6',
            'accent_color' => $brandingData->accent_color ?? '#10b981',
            'logo_url' => ($brandingData?->logo_path) ? asset('storage/' . $brandingData->logo_path) : null,
        ];

        return [
            'patient' => $patient,
            'links' => $links,
            'primaryLink' => $primaryLink,
            'professional' => $professional,
            'branding' => $branding,
            'permissions' => $permissions,
            'healthScore' => $patient->health_score ?? 0,
        ];
    }
}
