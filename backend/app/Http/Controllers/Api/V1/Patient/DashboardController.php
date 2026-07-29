<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalAppointment;
use App\Models\WeightEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $patient = auth()->user();
        $context = $request->attributes->get('active_patient_context') ?? $request->attributes->get('active_patient_link');
        $professionalId = $context->professional_id;

        $nextAppointment = ProfessionalAppointment::where('patient_id', $patient->id)
            ->where('professional_id', $professionalId)
            ->where('appointment_at', '>=', now())
            ->where('status', ProfessionalAppointment::STATUS_SCHEDULED)
            ->orderBy('appointment_at')
            ->first();

        $activePlan = $patient->trainingPlans()
            ->where('professional_id', $professionalId)
            ->where('is_active', true)
            ->latest()
            ->first();

        $lastWeight = WeightEntry::where('user_id', $patient->id)->latest('weighed_at')->first();

        return response()->json([
            'data' => [
                'summary' => [
                    'weight_kg' => $lastWeight ? (float) $lastWeight->weight_kg : null,
                    'next_appointment' => $nextAppointment ? $nextAppointment->appointment_at->toISOString() : null,
                    'active_plan' => $activePlan ? $activePlan->name : null,
                    'unread_messages' => 0, // Placeholder
                ]
            ]
        ]);
    }
}
