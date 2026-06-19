<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\ClinicProtocol;
use App\Models\ExerciseCatalog;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientTrainingController extends Controller
{
    /**
     * Lista os treinos de um paciente.
     */
    public function index(User $patient)
    {
        $user = Auth::user();
        
        // Verifica se o profissional tem vínculo com este paciente (Simplificado, ajuste conforme sua regra exata de vínculo)
        if (!$user->isAdministrator() && !$user->patients()->where('users.id', $patient->id)->exists()) {
            abort(403, 'Você não tem permissão para gerenciar treinos deste paciente.');
        }

        $plans = TrainingPlan::where('user_id', $patient->id)
            ->withCount('exercises')
            ->latest()
            ->get();

        $protocols = ClinicProtocol::where('academy_company_id', $user->academy_company_id)
            ->where('type', 'training') // Se houver distinção
            ->get();

        return view('professional.patient-trainings.index', compact('patient', 'plans', 'protocols'));
    }

    /**
     * Tela de criação de um novo plano para o paciente.
     */
    public function create(User $patient)
    {
        $user = Auth::user();

        if (!$user->isAdministrator() && !$user->patients()->where('users.id', $patient->id)->exists()) {
            abort(403, 'Acesso negado.');
        }

        $catalog = ExerciseCatalog::where('is_active', true)->with('muscles')->get()->groupBy('muscle_group');

        return view('professional.patient-trainings.create', compact('patient', 'catalog'));
    }

    /**
     * Salva o plano de treino vinculando ao paciente.
     */
    public function store(Request $request, User $patient)
    {
        $user = Auth::user();

        if (!$user->isAdministrator() && !$user->patients()->where('users.id', $patient->id)->exists()) {
            abort(403, 'Acesso negado.');
        }

        // Se vier via JSON (Alpine), decodifica para validar como array
        if ($request->filled('exercises_json')) {
            $exercises = json_decode($request->exercises_json, true);
            if (is_array($exercises)) {
                $request->merge(['exercises' => $exercises]);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'plan_label' => 'nullable|string|max:10',
            'goal' => 'nullable|string|max:50',
            'frequency' => 'nullable|integer|min:1|max:7',
            'difficulty' => 'nullable|string|max:20',
            'estimated_duration' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'student_profile' => 'nullable|string|max:30',
            'split_type' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:20',
            'days_of_week' => 'nullable|string',
            'total_volume' => 'nullable|numeric',
            'muscles_worked' => 'nullable|string',
            'exercises' => 'required|array|min:1',
            'exercises.*.id' => 'required|exists:exercises_catalog,id',
            'exercises.*.sets' => 'required|array|min:1',
        ]);

        $plan = TrainingPlan::create([
            'user_id' => $patient->id, // Paciente fará o treino
            'creator_id' => $user->id, // Profissional criou
            'professional_id' => $user->id, // Profissional responsável
            'name' => $validated['name'],
            'plan_label' => $request->plan_label,
            'goal' => $validated['goal'],
            'frequency' => $request->frequency,
            'difficulty' => $request->difficulty,
            'student_profile' => $validated['student_profile'],
            'split_type' => $validated['split_type'],
            'status' => $validated['status'] ?? 'Ativo',
            'days_of_week' => json_decode($request->days_of_week, true),
            'estimated_duration' => $request->estimated_duration,
            'total_volume' => $request->total_volume ?? 0,
            'muscles_worked' => json_decode($request->muscles_worked, true),
            'description' => $validated['description'],
        ]);

        foreach ($validated['exercises'] as $index => $exData) {
            $tpExercise = TrainingPlanExercise::create([
                'training_plan_id' => $plan->id,
                'exercise_id' => $exData['id'],
                'position' => $index,
            ]);

            foreach ($exData['sets'] as $setIndex => $setData) {
                $tpExercise->sets()->create([
                    'set_number' => $setIndex + 1,
                    'reps_target' => $setData['reps'] ?? 0,
                    'weight_target' => $setData['weight'] ?? 0,
                    'rest_seconds' => $setData['rest'] ?? 60,
                    'rpe_target' => $setData['rpe'] ?? null,
                    'cadence' => $setData['cadence'] ?? null,
                    'set_type' => $setData['type'] ?? 'work',
                ]);
            }
        }

        return redirect()->route('professional.patients.trainings.index', $patient->id)
            ->with('success', 'Plano de treino prescrito com sucesso para o paciente!');
    }

    /**
     * Aplica um protocolo existente ao paciente.
     */
    public function applyProtocol(Request $request, User $patient)
    {
        $user = Auth::user();

        if (!$user->isAdministrator() && !$user->patients()->where('users.id', $patient->id)->exists()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'protocol_id' => 'required|exists:clinic_protocols,id'
        ]);

        $protocol = ClinicProtocol::findOrFail($request->protocol_id);

        if ($protocol->academy_company_id !== $user->academy_company_id) {
            abort(403, 'Acesso negado ao protocolo.');
        }

        // Criar o TrainingPlan a partir do protocolo
        $plan = TrainingPlan::create([
            'user_id' => $patient->id,
            'creator_id' => $user->id,
            'professional_id' => $user->id,
            'name' => $protocol->name,
            'description' => "Protocolo Aplicado: " . $protocol->description . "\n\n" . $protocol->protocol,
            'goal' => $protocol->objective,
            'frequency' => (int) $protocol->frequency,
            'estimated_duration' => (int) $protocol->duration,
            'status' => 'Ativo',
            'is_template' => false,
        ]);

        // Idealmente, protocolos teriam seus próprios exercícios na modelagem de banco. 
        // Como o ClinicProtocol atual salva os dados de forma descritiva no campo 'protocol', 
        // a IA precisará ler isso ou o profissional pode editar depois.

        return redirect()->route('professional.patients.trainings.index', $patient->id)
            ->with('success', 'Protocolo aplicado ao paciente com sucesso. Revise os exercícios se necessário.');
    }
}


