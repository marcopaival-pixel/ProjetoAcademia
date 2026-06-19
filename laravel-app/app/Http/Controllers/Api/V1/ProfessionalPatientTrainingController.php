<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\FormatsTrainingPlans;
use App\Http\Controllers\Api\V1\Concerns\ResolvesProfessionalPatient;
use App\Http\Controllers\Controller;
use App\Models\ClinicProtocol;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalPatientTrainingController extends Controller
{
    use FormatsApiResponses;
    use FormatsTrainingPlans;
    use ResolvesProfessionalPatient;

    public function index(Request $request, int $patient): JsonResponse
    {
        $this->linkedPatient($request, $patient);

        $plans = TrainingPlan::query()
            ->where('user_id', $patient)
            ->withCount('exercises')
            ->latest()
            ->get()
            ->map(fn (TrainingPlan $plan) => $this->planSummary($plan))
            ->values()
            ->all();

        return $this->success(['plans' => $plans], ['count' => count($plans)]);
    }

    public function show(Request $request, int $patient, TrainingPlan $trainingPlan): JsonResponse
    {
        $this->linkedPatient($request, $patient);

        if ((int) $trainingPlan->user_id !== $patient) {
            return $this->error('Plano não pertence a este aluno.', 404, 'not_found');
        }

        $this->authorize('view', $trainingPlan);

        $trainingPlan->load(['exercises.catalogExercise', 'exercises.sets']);

        return $this->success($this->planDetail($trainingPlan));
    }

    public function store(Request $request, int $patient): JsonResponse
    {
        $linked = $this->linkedPatient($request, $patient);
        $professional = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'goal' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'frequency' => ['nullable', 'integer', 'min:1', 'max:7'],
            'protocol_id' => ['nullable', 'integer', 'exists:clinic_protocols,id'],
        ]);

        if (! empty($validated['protocol_id'])) {
            $plan = $this->applyProtocol($professional, $linked, (int) $validated['protocol_id']);

            return $this->success($this->planSummary($plan->loadCount('exercises')), status: 201);
        }

        $plan = TrainingPlan::create([
            'user_id' => $linked->id,
            'creator_id' => $professional->id,
            'professional_id' => $professional->id,
            'name' => $validated['name'],
            'goal' => $validated['goal'] ?? null,
            'description' => $validated['description'] ?? null,
            'frequency' => $validated['frequency'] ?? null,
            'status' => 'Ativo',
            'is_active' => true,
        ]);

        return $this->success($this->planSummary($plan->loadCount('exercises')), status: 201);
    }

    private function applyProtocol(User $professional, User $patient, int $protocolId): TrainingPlan
    {
        $protocol = ClinicProtocol::query()->findOrFail($protocolId);

        if ((int) $protocol->academy_company_id !== (int) $professional->academy_company_id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Acesso negado ao protocolo.');
        }

        return TrainingPlan::create([
            'user_id' => $patient->id,
            'creator_id' => $professional->id,
            'professional_id' => $professional->id,
            'name' => $protocol->name,
            'description' => 'Protocolo aplicado: '.($protocol->description ?? '')."\n\n".($protocol->protocol ?? ''),
            'goal' => $protocol->objective,
            'frequency' => (int) ($protocol->frequency ?? 0) ?: null,
            'estimated_duration' => (int) ($protocol->duration ?? 0) ?: null,
            'status' => 'Ativo',
            'is_active' => true,
            'is_template' => false,
        ]);
    }
}
