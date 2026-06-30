<?php

namespace App\Http\Controllers;

use App\Models\BodyAssessment;
use App\Services\Nutrition;
use App\Support\PatientAccessGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isPremium = $user->hasPremiumAccess();
        $tab = $request->get('tab', 'dashboard');
        
        $targetUserId = $user->id;
        if ($user->isProfessional()) {
            $activePatientId = PatientAccessGuard::resolveActivePatientId($user);
            if ($activePatientId) {
                $targetUserId = $activePatientId;
            }
        }

        $query = BodyAssessment::where('user_id', $targetUserId)
            ->orderBy('assessment_date', 'desc');

        if (!$isPremium) {
            $assessments = $query->limit(1)->get();
        } else {
            $assessments = $query->get();
        }

        // Dados para o gráfico de evolução (unindo pesos avulsos e avaliações)
        $weightQuery = \App\Models\WeightEntry::where('user_id', $targetUserId)
            ->orderBy('weighed_at', 'asc');
        if (! $isPremium && (int) $targetUserId === (int) $user->id) {
            $weightQuery->where('weighed_at', '>=', now()->subDays(30));
        }
        $weightEntries = $weightQuery->get();

        $chartData = $weightEntries->map(fn($e) => [
            'date' => $e->weighed_at,
            'weight' => $e->weight_kg
        ]);

        $targetUser = \App\Models\User::find($targetUserId);

        return view('assessments.index', compact('assessments', 'tab', 'chartData', 'isPremium', 'targetUser'));
    }

    public function create(): View
    {
        $user = Auth::user();
        
        $targetUserId = $user->id;
        if ($user->isProfessional()) {
            $activePatientId = PatientAccessGuard::resolveActivePatientId($user);
            if ($activePatientId) {
                $targetUserId = $activePatientId;
            }
        }
        
        $targetUser = \App\Models\User::findOrFail($targetUserId);
        $maxAssessments = $targetUser->getPlanLimit('max_assessments');
        
        if ($maxAssessments > 0) {
            $count = BodyAssessment::where('user_id', $targetUserId)->count();
            if ($count >= $maxAssessments) {
                return redirect()->route('assessments.index')
                    ->with('error', "O limite de {$maxAssessments} avaliações do plano foi atingido. Faça upgrade para continuar evoluindo!");
            }
        }

        $professionals = $user->professionals; // Para os pacientes escolherem enviar
        return view('assessments.create', compact('professionals', 'targetUser'));
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
            'icw_l' => 'nullable|numeric',
            'ecw_l' => 'nullable|numeric',
            'dry_lean_mass_kg' => 'nullable|numeric',
            'body_fat_mass_kg' => 'nullable|numeric',
            'segmental_lean_arm_l' => 'nullable|numeric',
            'segmental_lean_arm_r' => 'nullable|numeric',
            'segmental_lean_leg_l' => 'nullable|numeric',
            'segmental_lean_leg_r' => 'nullable|numeric',
            'segmental_lean_trunk' => 'nullable|numeric',
            'visceral_fat_level' => 'nullable|integer',
            'basal_metabolic_rate' => 'nullable|integer',
            'phase_angle' => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $isProfessional = $user->hasRole(['professional', 'instructor', 'supervisor']);

        if (! $user->hasPremiumAccess() && ! $isProfessional) {
            foreach ([
                'icw_l', 'ecw_l', 'dry_lean_mass_kg', 'body_fat_mass_kg',
                'segmental_lean_arm_l', 'segmental_lean_arm_r', 'segmental_lean_leg_l',
                'segmental_lean_leg_r', 'segmental_lean_trunk', 'visceral_fat_level',
                'basal_metabolic_rate', 'phase_angle',
            ] as $bioField) {
                unset($data[$bioField]);
            }
        }

        $patientId = Auth::id();
        
        if ($isProfessional) {
            if (session()->has('active_patient_id')) {
                $activePatientId = session('active_patient_id');
                // Verifica se o profissional tem vínculo com o paciente
                if (Auth::user()->patients()->wherePivot('user_id', $activePatientId)->exists()) {
                    $patientId = $activePatientId;
                    $data['created_by'] = 'professional';
                    $data['professional_id'] = Auth::id();
                    $data['status'] = 'approved';
                } else {
                    return back()->with('error', 'Acesso negado a este paciente.');
                }
            } else {
                return back()->with('error', 'Selecione um paciente para registrar a avaliação.');
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
            if ($user->consumeAiCredit('generate_workout')) {
                $generator = app(\App\Services\AIFitnessGeneratorService::class);
                $generator->generateTrainingPlan($user);
            } else {
                return redirect()->route('assessments.index')->with('error', 'Avaliação salva, mas créditos insuficientes para gerar o treino IA.');
            }
        }

        // Geração de Plano Alimentar IA (Opcional)
        if ($request->has('generate_ai_meal_plan')) {
            if ($user->consumeAiCredit('generate_diet')) {
                $generator = app(\App\Services\AIFitnessGeneratorService::class);
                $mealPlan = $generator->generateMealPlan($user);
                if ($mealPlan['ok']) {
                    $assessment->update(['ai_suggestions' => $mealPlan['plan']]);
                }
            } else {
                return redirect()->route('assessments.index')->with('warning', 'Avaliação salva, mas créditos insuficientes para gerar o plano alimentar IA.');
            }
        }

        if ($isProfessional) {
            return redirect()->route('assessments.index')->with('success', 'Avaliação física e análise inteligente registradas!');
        }

        return redirect()->route('assessments.index')->with('success', 'Avaliação registrada! Seu Score de Saúde foi atualizado para ' . $healthScore . '%.');
    }

    public function show(BodyAssessment $assessment)
    {
        $user = Auth::user();
        $isPremium = $user->hasPremiumAccess();

        $targetUserId = $user->id;
        if ($user->isProfessional() && session()->has('active_patient_id')) {
            $targetUserId = session('active_patient_id');
        }

        // Se não for premium, só pode ver a última
        if (!$isPremium) {
            $latestId = BodyAssessment::where('user_id', $targetUserId)
                ->latest('assessment_date')
                ->latest('id')
                ->value('id');

            if ($assessment->id !== $latestId) {
                return redirect()->route('assessments.index')
                    ->with('premium_locked', true);
            }
        }

        $this->authorize('view', $assessment);

        $motor = app(\App\Services\IntelligenceMotorService::class);
        $owner = \App\Models\User::find($assessment->user_id);
        
        $predictions = $motor->predictEvolution($owner);
        $risks = $motor->detectRisks($owner);
        $healthScore = $owner->health_score;
        $bioInsights = $motor->analyzeBioimpedance($assessment);

        return view('assessments.show', compact('assessment', 'predictions', 'risks', 'healthScore', 'bioInsights', 'isPremium'));
    }

    public function destroy(BodyAssessment $assessment)
    {
        $user = Auth::user();
        
        $targetUserId = $user->id;
        if ($user->isProfessional() && session()->has('active_patient_id')) {
            $targetUserId = session('active_patient_id');
        }

        $this->authorize('delete', $assessment);

        if (!$user->hasPremiumAccess()) {
             $latestId = BodyAssessment::where('user_id', $targetUserId)
                ->latest('assessment_date')
                ->latest('id')
                ->value('id');

            if ($assessment->id !== $latestId) {
                return redirect()->route('assessments.index')
                    ->with('error', 'Apenas o registro mais recente pode ser gerenciado no plano gratuito.');
            }
        }

        $assessment->delete();
        return redirect()->route('assessments.index')->with('success', 'Avaliação removida com sucesso!');
    }
}
