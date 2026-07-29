<?php

namespace App\Http\Controllers\Api\V1\Professional;

use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use App\Models\HealthAlert;
use App\Models\MealTemplate;
use App\Models\ProfessionalAppointment;
use App\Models\TrainingPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $professional = $request->user();
        $uid = $professional->id;

        $patientsQuery = $professional->patients()->where('pacientes.status', 'Sim');
        $patientIds = $patientsQuery->pluck('users.id');

        $todayAppointments = ProfessionalAppointment::with('patient:id,name')
            ->where('professional_id', $uid)
            ->whereDate('appointment_at', now()->toDateString())
            ->orderBy('appointment_at')
            ->get()
            ->map(fn ($a) => $this->appointmentPayload($a));

        $nextAppointments = ProfessionalAppointment::with('patient:id,name')
            ->where('professional_id', $uid)
            ->where('appointment_at', '>', now())
            ->orderBy('appointment_at')
            ->limit(5)
            ->get()
            ->map(fn ($a) => $this->appointmentPayload($a));

        $unreadAlerts = HealthAlert::query()
            ->whereIn('user_id', $patientIds)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'data' => [
                'metrics' => [
                    'total_patients' => $patientsQuery->count(),
                    'active_patients_7d' => $professional->patients()
                        ->where('pacientes.status', 'Sim')
                        ->where('last_activity_at', '>=', now()->subDays(7))
                        ->count(),
                    'pending_assessments' => BodyAssessment::withoutGlobalScope('professional_access')
                        ->where('professional_id', $uid)->where('status', 'pending')->count(),
                    'active_workouts' => TrainingPlan::withoutGlobalScope('professional_access')
                        ->where('creator_id', $uid)->where('is_active', true)->count(),
                    'active_diets' => MealTemplate::withoutGlobalScope('professional_access')
                        ->where('user_id', $uid)->count(),
                    'unread_alerts' => $unreadAlerts,
                ],
                'today_appointments' => $todayAppointments,
                'next_appointments' => $nextAppointments,
            ],
        ]);
    }

    private function appointmentPayload(ProfessionalAppointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'patient_name' => $appointment->patient?->name,
            'appointment_at' => $appointment->appointment_at?->toIso8601String(),
            'status' => $appointment->status,
            'service_type' => $appointment->service_type,
        ];
    }
}
