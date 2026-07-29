<?php

namespace App\Http\Controllers\Api\V1\Professional;

use App\Http\Controllers\Api\V1\Professional\Concerns\ResolvesProfessionalPatient;
use App\Http\Controllers\Controller;
use App\Models\ProfessionalPatientRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    use ResolvesProfessionalPatient;

    public function index(Request $request): JsonResponse
    {
        $professional = $request->user();
        $search = $request->query('search');

        $query = $professional->patients()
            ->where('pacientes.status', 'Sim')
            ->with('profile');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('users.name')->limit(100)->get()->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'goal' => $user->profile?->goal,
            'last_activity_at' => optional($user->last_activity_at)->toIso8601String(),
        ]);

        return response()->json(['data' => ['patients' => $patients]]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $patient = $this->assertProfessionalPatient($request, $id);
        $patient->load('profile');

        $latestAssessment = $patient->assessments()
            ->where('professional_id', $request->user()->id)
            ->latest('assessment_date')
            ->first();

        return response()->json([
            'data' => [
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'profile' => [
                        'goal' => $patient->profile?->goal,
                        'sex' => $patient->profile?->sex,
                        'height_cm' => $patient->profile?->height_cm,
                        'target_weight_kg' => $patient->profile?->target_weight_kg,
                    ],
                    'latest_assessment' => $latestAssessment ? [
                        'id' => $latestAssessment->id,
                        'assessment_date' => optional($latestAssessment->assessment_date)->toDateString(),
                        'weight_kg' => $latestAssessment->weight_kg,
                        'bf_percent' => $latestAssessment->bf_percent,
                    ] : null,
                ],
            ],
        ]);
    }

    public function requests(Request $request): JsonResponse
    {
        $requests = ProfessionalPatientRequest::query()
            ->where('professional_id', $request->user()->id)
            ->where('status', 'pending')
            ->with('patient:id,name,email')
            ->latest()
            ->get()
            ->map(fn (ProfessionalPatientRequest $item) => [
                'id' => $item->id,
                'patient_id' => $item->patient_id,
                'patient_name' => $item->patient?->name,
                'patient_email' => $item->patient?->email,
                'message' => $item->message,
                'created_at' => optional($item->created_at)->toIso8601String(),
            ]);

        return response()->json(['data' => ['requests' => $requests]]);
    }

    public function approveRequest(Request $request, int $id): JsonResponse
    {
        $linkRequest = ProfessionalPatientRequest::query()
            ->where('professional_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $professional = $request->user();
        $currentCount = $professional->patients()->where('pacientes.status', 'Sim')->count();
        $maxPatients = $professional->professionalPlan?->max_patients ?? 50;

        if ($maxPatients !== -1 && $currentCount >= $maxPatients) {
            return response()->json(['message' => 'Limite de pacientes atingido para o seu plano.'], 422);
        }

        DB::transaction(function () use ($linkRequest, $professional) {
            $linkRequest->update(['status' => 'approved']);

            if ($linkRequest->message === 'Transferência') {
                \App\Models\ProfessionalPatient::where('user_id', $linkRequest->patient_id)
                    ->where('profissional_id', '!=', $professional->id)
                    ->update(['status' => 'Não']);
            }

            $professional->patients()->syncWithoutDetaching([
                $linkRequest->patient_id => [
                    'status' => 'Sim',
                    'data_cadastro' => now(),
                    'empresa_id' => $professional->academy_company_id,
                ],
            ]);

            $linkRequest->patient?->notify(
                new \App\Notifications\PatientProfessionalLinkNotification($professional->name, 'new')
            );
        });

        return response()->json(['message' => 'Solicitação aprovada.']);
    }

    public function rejectRequest(Request $request, int $id): JsonResponse
    {
        $linkRequest = ProfessionalPatientRequest::query()
            ->where('professional_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $linkRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Solicitação rejeitada.']);
    }
}
