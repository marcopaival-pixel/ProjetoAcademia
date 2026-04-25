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

        $data['user_id'] = Auth::id();
        
        if (!empty($data['professional_id'])) {
            $data['status'] = 'pending';
            $data['created_by'] = 'patient';
        } else {
            $data['status'] = 'approved';
            $data['created_by'] = 'patient';
        }

        $assessment = BodyAssessment::create($data);

        // Auto-calcular BF% se measurements estiverem presentes (Máxima Eficácia)
        $profile = Auth::user()->profile;
        if ($assessment->bf_percent === null && $profile && $profile->height_cm > 0) {
            $calcBf = Nutrition::calculateBodyFatPercent(
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

        // Sincronizar com WeightEntry e atualizar metas automáticas (Máxima Eficácia)
        if (!empty($data['weight_kg'])) {
            \App\Models\WeightEntry::updateOrCreate(
                ['user_id' => $data['user_id'], 'weighed_at' => $data['assessment_date']],
                ['weight_kg' => $data['weight_kg']]
            );

            $profile = Auth::user()->profile;
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

        return redirect()->route('assessments.index')->with('success', 'Avaliação física registrada com sucesso!');
    }

    public function show(BodyAssessment $assessment): View
    {
        if ($assessment->user_id !== Auth::id()) abort(403);
        return view('assessments.show', compact('assessment'));
    }

    public function destroy(BodyAssessment $assessment)
    {
        if ($assessment->user_id !== Auth::id()) abort(403);
        $assessment->delete();
        return back()->with('success', 'Avaliação removida.');
    }
}
