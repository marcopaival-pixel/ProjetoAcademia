<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI;

use App\Models\Especialidade;
use App\Models\PrescriptionTemplate;
use App\Models\ClinicProtocol;
use App\Models\MedicalPrescription;
use App\Models\MedicalHistory;
use App\Models\User;

class AIPrescriptionController extends Controller
{
    /**
     * Exibe o Assistente de Prescrição Dinâmica.
     */
    public function index()
    {
        $user = auth()->user();
        $user->load('academyCompany');

        // Profissionais veem seus pacientes (que podem ter role 'aluno' ou 'paciente')
        if ($user->hasRole(['professional', 'instructor', 'supervisor'])) {
            $patients = $user->patients()
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['aluno', 'paciente']);
                })
                ->orderBy('name')
                ->get();
        } else {
            // Administradores veem todos os alunos e pacientes
            $patients = User::whereHas('roles', function($q) {
                    $q->whereIn('name', ['aluno', 'paciente']);
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

        $actionType = $request->type === 'training' ? 'generate_workout' : ($request->type === 'nutrition' ? 'generate_diet' : 'generate_report');
        if (!$user->consumeAiCredit($actionType, ['patient_id' => $request->patient_id])) {
            return response()->json([
                'success' => false, 
                'error' => 'Créditos insuficientes. Adquira mais créditos para continuar gerando prescrições inteligentes.'
            ], 403);
        }

        $patient = User::with(['profile', 'assessments', 'loadLogs'])->findOrFail($request->patient_id);
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

        try {
            $client = OpenAI::client($apiKey);
            
            $persona = $specialty->nome;
            $systemPrompt = "Você é um especialista em {$persona}. Gere uma prescrição/estratégia personalizada baseada nos dados do paciente: " . json_encode($context) . ". ";
            
            if ($request->type === 'training') {
                $systemPrompt .= "Retorne JSON com: 'name', 'description', 'exercises' (array de {name, sets, reps, notes}).";
            } elseif ($request->type === 'nutrition') {
                $systemPrompt .= "Retorne JSON com: 'name', 'strategy', 'meals' (array de {time, foods, macros_est}).";
            } else {
                $systemPrompt .= "Retorne JSON com: 'name', 'objective', 'protocol', 'frequency', 'duration', 'medicine', 'dosage', 'observations'.";
            }

            $response = $client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $request->prompt ?? "Crie a melhor estratégia para o perfil acima."],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            return response()->json([
                'success' => true,
                'data' => json_decode($response->choices[0]->message->content, true),
                'context' => $context
            ]);

        } catch (\Exception $e) {
            Log::error('Erro AI Prescription: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Falha no motor de IA.'], 500);
        }
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
