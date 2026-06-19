<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Especialidade;
use App\Models\PrescriptionTemplate;
use App\Models\ClinicProtocol;
use App\Models\MedicalPrescription;
use App\Models\MedicalHistory;
use App\Models\User;
use App\Services\AI\OrchestratorService;
use App\Support\PatientAccessGuard;

class AIPrescriptionController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}
    /**
     * Exibe o Assistente de Prescrição Dinâmica.
     */
    public function index()
    {
        $user = auth()->user();
        $user->load('academyCompany');

        // Profissionais veem seus pacientes (que podem ter role 'paciente' ou 'paciente')
        if ($user->hasRole(['professional', 'instructor', 'supervisor'])) {
            $patients = $user->patients()
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['paciente', 'paciente']);
                })
                ->orderBy('name')
                ->get();
        } else {
            // Administradores veem todos os pacientes e pacientes
            $patients = User::whereHas('roles', function($q) {
                    $q->whereIn('name', ['paciente', 'paciente']);
                })
                ->orderBy('name')
                ->get();
        }

        $specialties = Especialidade::active()->orderBy('nome')->get();
        
        $templates = PrescriptionTemplate::where('professional_id', $user->id)
            ->orWhereNull('professional_id') // System templates if any
            ->get();

        $clinicProtocols = [];
        if ($user->academy_company_id) {
            $clinicProtocols = ClinicProtocol::where('academy_company_id', $user->academy_company_id)->get();
        }

        return view('professional.ai-wizard', compact('patients', 'specialties', 'templates', 'clinicProtocols'));
    }


    /**
     * Gera uma sugestão de plano via IA.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'specialty_id' => 'required|exists:especialidades,id',
            'type' => 'required|in:training,nutrition,medical',
            'prompt' => 'nullable|string',
        ]);

        $user = auth()->user();
        $featureKey = $request->type === 'training' ? 'ai_training' : 'ai_nutrition';

        if (!$user->hasFeature($featureKey)) {
            return response()->json([
                'success' => false, 
                'error' => 'Funcionalidade disponível apenas no plano Pro. Faça upgrade agora!'
            ], 403);
        }

        $featureCode = match ($request->type) {
            'training' => 'generate_workout',
            'nutrition' => 'generate_diet',
            default => 'generate_report',
        };

        $patient = PatientAccessGuard::assertProfessionalPatientLink(
            $user,
            (int) $request->patient_id
        );
        $patient->load(['profile', 'assessments', 'loadLogs']);
        $specialty = Especialidade::find($request->specialty_id);
        $profile = $patient->profile;
        $latestAss = $patient->assessments()->orderBy('assessment_date', 'desc')->first();
        
        // Data context for IA
        $context = [
            'age' => $profile ? \Carbon\Carbon::parse($profile->birth_date)->age : 'Desconhecida',
            'sex' => $profile ? ($profile->sex === 'M' ? 'Masculino' : 'Feminino') : 'Não informado',
            'weight' => $latestAss ? $latestAss->weight_kg : ($profile->weight_kg ?? 'N/A'),
            'bf_percent' => $latestAss ? $latestAss->bf_percent : 'Não medido',
            'goal' => $profile->goal ?? 'Manutenção',
            'activity_level' => $profile->activity_level ?? 'Moderado',
            'specialty' => $specialty->nome,
        ];

        $apiKey = env('OPENAI_API_KEY');
        
        if (!$apiKey) {
            return response()->json([
                'success' => true,
                'is_mock' => true,
                'plan_name' => "Prescrição: {$specialty->nome}",
                'content' => $this->getMockResponse($request->type, $request->prompt ?? 'Geral', $specialty->nome),
                'context_used' => $context
            ]);
        }

        $intent = $request->type === 'training' ? 'training' : ($request->type === 'nutrition' ? 'nutrition' : 'clinical');

        $result = $this->orchestrator->run($user, $request->prompt ?? 'Gere uma estratégia personalizada.', [
            'intent' => $intent,
            'patient_id' => $patient->id,
            'specialty' => $specialty->nome,
            'clinicId' => $user->academy_company_id,
            'clinic_id' => $user->clinic_id,
            'feature_code' => $featureCode,
            'response_format' => 'json',
        ]);

        if ($result['status'] === 'success') {
            return response()->json([
                'success' => true,
                'data' => is_string($result['message']) ? json_decode($result['message'], true) : $result['message'],
                'context' => $context,
            ]);
        }

        if ($result['status'] === 'limit_reached') {
            return response()->json([
                'success' => false,
                'error' => $result['message'] ?? 'Créditos insuficientes.',
            ], 403);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Falha no motor de IA NexShape.',
        ], 500);
    }

    /**
     * Salva a prescrição no prontuário.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'especialidade_id' => 'required|exists:especialidades,id',
            'date' => 'required|date',
            'objective' => 'nullable|string',
            'protocol' => 'nullable|string',
            'medicine' => 'required|string',
            'dosage' => 'nullable|string',
            'frequency' => 'nullable|string',
            'duration' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $user = auth()->user();

        PatientAccessGuard::assertProfessionalPatientLink($user, (int) $validated['patient_id']);

        $prescription = MedicalPrescription::create(array_merge($validated, [
            'professional_id' => $user->id,
            'academy_company_id' => $user->academy_company_id,
        ]));

        MedicalHistory::log($validated['patient_id'], 'create', 'prescription', "Prescrição gerada via AI Wizard: {$prescription->medicine} ({$prescription->specialty?->nome})");

        return response()->json([
            'success' => true,
            'message' => 'Prescrição salva com sucesso no prontuário.',
            'prescription_id' => $prescription->id
        ]);
    }

    private function getMockResponse($type, $userPrompt, $specialty = 'Geral')
    {
        if ($type === 'training') {
            return [
                'name' => "Treino: {$specialty}",
                'description' => 'Plano gerado baseado na sua solicitação: ' . $userPrompt,
                'exercises' => [
                    ['name' => 'Supino Inclinado (Halteres)', 'sets' => '4', 'reps' => '8-10', 'notes' => 'Foco na cadência 3010'],
                    ['name' => 'Remada Curvada', 'sets' => '3', 'reps' => '12', 'notes' => 'Pico de contração de 2 segundos'],
                    ['name' => 'Agachamento Búlgaro', 'sets' => '3', 'reps' => '10 cada perna', 'notes' => 'Aumentar carga progressivamente'],
                ]
            ];
        }

        if ($type === 'nutrition') {
            return [
                'name' => "Estratégia Nutricional: {$specialty}",
                'strategy' => 'Foco em densidade nutricional e controle glicêmico.',
                'meals' => [
                    ['time' => '07:00 - Desjejum', 'foods' => 'Omelete de 3 ovos com espinafre e queijo cottage', 'macros' => 'P: 25g, C: 4g, G: 18g'],
                    ['time' => '12:30 - Almoço', 'foods' => 'Peixe grelhado, brócolis ao vapor e mix de castanhas', 'macros' => 'P: 35g, C: 12g, G: 22g'],
                ]
            ];
        }

        return [
            'name' => "Prescrição Clínica: {$specialty}",
            'objective' => 'Controle de sintomas e estabilização metabólica.',
            'protocol' => 'Protocolo Padrão NexShape v3',
            'frequency' => '1x ao dia',
            'duration' => '30 dias',
            'medicine' => 'Medicamento/Suplemento Sugerido',
            'dosage' => '1 cápsula',
            'observations' => 'Observação clínica importante baseada no prompt: ' . $userPrompt
        ];
    }
}


