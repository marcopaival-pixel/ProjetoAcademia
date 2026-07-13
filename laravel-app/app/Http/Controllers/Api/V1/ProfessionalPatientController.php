<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalPatientController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $professional = $request->user();
        $search = $request->query('search');

        $query = $professional->patients()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['paciente', 'aluno']))
            ->wherePivot('status', 'Sim')
            ->with('profile');

        if (is_string($search) && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $patients = $query
            ->orderBy('users.name')
            ->get()
            ->map(fn (User $patient): array => $this->formatPatientSummary($patient))
            ->values()
            ->all();

        return $this->success(['patients' => $patients], ['count' => count($patients)]);
    }

    public function show(Request $request, int $patient): JsonResponse
    {
        $professional = $request->user();

        $linked = $professional->patients()
            ->where('users.id', $patient)
            ->wherePivot('status', 'Sim')
            ->with(['profile', 'weightEntries' => fn ($q) => $q->orderByDesc('weighed_at')->limit(1)])
            ->first();

        if (! $linked instanceof User) {
            return $this->error('Sem vínculo com este aluno.', 403, 'forbidden');
        }

        $latestAssessment = $linked->assessments()
            ->orderByDesc('assessment_date')
            ->first();

        return $this->success([
            'patient' => array_merge($this->formatPatientSummary($linked), [
                'goal' => $linked->profile?->goal,
                'birth_date' => $linked->profile?->birth_date?->toDateString(),
                'last_weight_kg' => $linked->weightEntries->first()?->weight_kg,
                'last_assessment_date' => $latestAssessment?->assessment_date?->toDateString(),
                'last_bf_percent' => $latestAssessment?->bf_percent,
            ]),
        ]);
    }

    public function requests(Request $request): JsonResponse
    {
        $requests = $request->user()->receivedRequests()
            ->with('patient')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (\App\Models\ProfessionalPatientRequest $req) {
                return [
                    'id' => $req->id,
                    'patient_id' => $req->patient_id,
                    'patient_name' => $req->patient?->name,
                    'patient_email' => $req->patient?->email,
                    'message' => $req->message,
                    'status' => $req->status,
                    'created_at' => $req->created_at?->toIso8601String(),
                ];
            });

        return $this->success(['requests' => $requests]);
    }

    public function approveRequest(Request $request, int $id): JsonResponse
    {
        $linkRequest = \App\Models\ProfessionalPatientRequest::where('professional_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $professional = $request->user();
        
        $currentPatientsCount = $professional->patients()->count();
        $maxPatients = $professional->professionalPlan ? $professional->professionalPlan->max_patients : 50;

        if ($maxPatients !== -1 && $currentPatientsCount >= $maxPatients) {
            return $this->error('Limite de pacientes atingido para o seu plano atual.', 422, 'limit_exceeded');
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($linkRequest, $professional) {
            $linkRequest->update(['status' => 'approved']);

            if ($linkRequest->message === 'Transferência') {
                \App\Models\ProfessionalPatient::where('user_id', $linkRequest->patient_id)
                    ->where('professional_id', '!=', $professional->id)
                    ->update(['status' => 'Não']);
            }

            $professional->patients()->syncWithoutDetaching([
                $linkRequest->patient_id => [
                    'status' => 'Sim',
                    'data_cadastro' => now(),
                    'empresa_id' => $professional->academy_company_id
                ]
            ]);

            $linkRequest->patient->notify(new \App\Notifications\PatientProfessionalLinkNotification($professional->name, 'new'));
        });

        return $this->success([
            'message' => 'Solicitação aprovada e paciente vinculado com sucesso.'
        ]);
    }

    public function rejectRequest(Request $request, int $id): JsonResponse
    {
        $linkRequest = \App\Models\ProfessionalPatientRequest::where('professional_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $linkRequest->update(['status' => 'rejected']);

        return $this->success([
            'message' => 'Solicitação rejeitada com sucesso.'
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatPatientSummary(User $patient): array
    {
        $linkStatus = 'Inativo';
        /** @var \Illuminate\Database\Eloquent\Relations\Pivot|null $pivot */
        $pivot = $patient->pivot ?? null;
        if ($pivot && ($pivot->status ?? null) === 'Sim') {
            $linkStatus = $patient->status === 'pending' ? 'Pendente' : 'Ativo';
        }

        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'email' => $patient->email,
            'status' => $linkStatus,
            'last_activity_at' => $patient->last_activity_at instanceof CarbonInterface
                ? $patient->last_activity_at->toIso8601String()
                : $patient->last_activity_at,
        ];
    }
}
