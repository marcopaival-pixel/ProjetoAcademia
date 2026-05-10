<?php

namespace App\Http\Controllers;

use App\Models\BodyAssessment;
use App\Services\Nutrition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'dashboard');
        
        $assessments = BodyAssessment::where('user_id', $user->id)
            ->orderBy('assessment_date', 'desc')
            ->get();

        // Dados para o gráfico de evolução (unindo pesos avulsos e avaliações)
        $weightEntries = \App\Models\WeightEntry::where('user_id', $user->id)
            ->orderBy('weighed_at', 'asc')
            ->get();

        $chartData = $weightEntries->map(fn($e) => [
            'date' => $e->weighed_at,
            'weight' => $e->weight_kg
        ]);

        return view('assessments.index', compact('assessments', 'tab', 'chartData'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $maxAssessments = $user->getPlanLimit('max_assessments');
        
        if ($maxAssessments > 0) {
            $count = BodyAssessment::where('user_id', $user->id)->count();
            if ($count >= $maxAssessments) {
                return redirect()->route('assessments.index')
                    ->with('error', "Você atingiu o limite de {$maxAssessments} avaliações no seu plano. Faça upgrade para continuar evoluindo!");
            }
        }

        $professionals = $user->professionals;
        return view('assessments.create', compact('professionals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'assessment_date' => 'required|date',
            'weight_kg' => 'nullable|numeric|min:20|max:500',
            'bf_percent' => 'nullable|numeric|min:1|max:70',
            'muscle_percent' => 'nullable|numeric|min:1|max:90',
            'neck' => 'nullable|numeric',
            'chest' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'abdomen' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
            'bicep_l' => 'nullable|numeric',
            'bicep_r' => 'nullable|numeric',
            'forearm_l' => 'nullable|numeric',
            'forearm_r' => 'nullable|numeric',
            'thigh_l' => 'nullable|numeric',
            'thigh_r' => 'nullable|numeric',
            'calf_l' => 'nullable|numeric',
            'calf_r' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'professional_id' => 'nullable|exists:users,id',
            'blood_pressure' => 'nullable|string',
            'heart_rate' => 'nullable|integer',
        ]);

        $patientId = Auth::id();
        $isProfessional = Auth::user()->hasRole(['professional', 'instructor', 'supervisor']);
        
        if ($isProfessional && $request->filled('patient_id')) {
            // Verifica se o profissional tem vínculo com o paciente
            if (Auth::user()->patients()->wherePivot('user_id', $request->patient_id)->exists()) {
                $patientId = $request->patient_id;
                $data['created_by'] = 'professional';
                $data['professional_id'] = Auth::id();
                $data['status'] = 'approved';
            } else {
                return back()->with('error', 'Acesso negado a este paciente.');
            }
        } else {
            if (!empty($data['professional_id'])) {
                $data['status'] = 'pending';
                $data['created_by'] = 'patient';
            } else {
                $data['status'] = 'approved';
                $data['created_by'] = 'patient';
            }
        }
        
        $data['user_id'] = $patientId;

        $assessment = BodyAssessment::create($data);

        // Atualizar dados de Rotina e Fitness no Perfil do Usuário
        $profile = \App\Models\UserProfile::where('user_id', $patientId)->first();
        if ($profile) {
            $profile->update($request->only([
                'physical_level', 'experience_level', 'training_location', 
                'cardio_frequency', 'sleep_hours', 'nutrition_quality', 
                'available_daily_time_mins', 'fitness_notes', 'target_weight_kg'
            ]));
        }

        // Executar Motor Inteligente (Máxima Eficácia)
        $motor = app(\App\Services\IntelligenceMotorService::class);
        $user = \App\Models\User::find($patientId);
        
        // Calcular BF% se measurements estiverem presentes
        if ($assessment->bf_percent === null && $profile && $profile->height_cm > 0) {
            $calcBf = \App\Services\Nutrition::calculateBodyFatPercent(
                $profile->sex,
                (float)$profile->height_cm,
                (float)$assessment->neck,
                (float)$assessment->waist,
                (float)$assessment->hips
            );
            
            if ($calcBf !== null) {
                $assessment->update(['bf_percent' => $calcBf]);
            }
        }

        // Atualizar Health Score do Usuário
        $healthScore = $motor->calculateHealthScore($user);
        $user->update(['health_score' => $healthScore]);

        // Sincronizar com WeightEntry e atualizar metas automáticas
        if (!empty($data['weight_kg'])) {
            \App\Models\WeightEntry::updateOrCreate(
                ['user_id' => $data['user_id'], 'weighed_at' => $data['assessment_date']],
                ['weight_kg' => $data['weight_kg']]
            );

            if ($profile && $profile->is_water_target_auto) {
                $newWaterTarget = \App\Services\Nutrition::calculateWaterTarget(
                    (float)$data['weight_kg'],
                    $profile->birth_date?->toDateString(),
                    $profile->sex,
                    $profile->activity_level,
                    $profile->climate ?? 'moderate'
                );
                $profile->update(['water_target_ml' => $newWaterTarget]);
            }
        }

        // Geração de Treino IA (Opcional)
        if ($request->has('generate_ai_training')) {
            $generator = app(\App\Services\AIFitnessGeneratorService::class);
            $generator->generateTrainingPlan($user);
        }

        // Geração de Plano Alimentar IA (Opcional)
        if ($request->has('generate_ai_meal_plan')) {
            $generator = app(\App\Services\AIFitnessGeneratorService::class);
            $mealPlan = $generator->generateMealPlan($user);
            if ($mealPlan['ok']) {
                $assessment->update(['ai_suggestions' => $mealPlan['plan']]);
            }
        }

        if ($isProfessional && $request->filled('patient_id')) {
            return redirect()->route('professional.patients.show', $patientId)->with('success', 'Avaliação física e análise inteligente registradas!');
        }

        return redirect()->route('assessments.index')->with('success', 'Avaliação registrada! Seu Score de Saúde foi atualizado para ' . $healthScore . '%.');
    }

    public function show(BodyAssessment $assessment): View
    {
        if ($assessment->user_id !== Auth::id() && !Auth::user()->hasRole(['professional', 'admin'])) abort(403);

        if (Auth::user()->isResourceOverLimit('assessments', $assessment->id)) {
            return redirect()->route('assessments.index')->with('error', 'Esta avaliação está bloqueada por exceder o limite do seu plano atual. Faça upgrade para acessá-la.');
        }

        $motor = app(\App\Services\IntelligenceMotorService::class);
        $user = \App\Models\User::find($assessment->user_id);
        
        $predictions = $motor->predictEvolution($user);
        $risks = $motor->detectRisks($user);
        $healthScore = $user->health_score;

        return view('assessments.show', compact('assessment', 'predictions', 'risks', 'healthScore'));
    }

    public function destroy(BodyAssessment $assessment)
    {
        if ($assessment->user_id !== Auth::id()) abort(403);

        if (Auth::user()->isResourceOverLimit('assessments', $assessment->id)) {
            return back()->with('error', 'Esta avaliação está bloqueada por exceder o limite do seu plano atual. Faça upgrade para gerenciá-la.');
        }

        $assessment->delete();
        return back()->with('success', 'Avaliação removida.');
    }
}
