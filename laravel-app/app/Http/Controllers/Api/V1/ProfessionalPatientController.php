<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\User;
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

        if ($linked === null) {
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

    /**
     * @return array<string, mixed>
     */
    private function formatPatientSummary(User $patient): array
    {
        $linkStatus = 'Inativo';
        if ($patient->pivot?->status === 'Sim') {
            $linkStatus = $patient->status === 'pending' ? 'Pendente' : 'Ativo';
        }

        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'email' => $patient->email,
            'status' => $linkStatus,
            'last_activity_at' => $patient->last_activity_at?->toIso8601String(),
        ];
    }
}
