<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use App\Models\HealthAlert;
use App\Models\ProfessionalAppointment;
use App\Models\TrainingPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalDashboardController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $professional = $request->user();
        $uid = $professional->id;

        $totalPatients = $professional->patients()->wherePivot('status', 'Sim')->count();
        $activePatients = $professional->patients()
            ->wherePivot('status', 'Sim')
            ->where('last_activity_at', '>=', now()->subDays(30))
            ->count();

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todayAppointments = ProfessionalAppointment::query()
            ->where('professional_id', $uid)
            ->whereBetween('appointment_at', [$todayStart, $todayEnd])
            ->count();

        $pendingAppointments = ProfessionalAppointment::query()
            ->where('professional_id', $uid)
            ->where('appointment_at', '>=', $todayStart)
            ->where('status', ProfessionalAppointment::STATUS_SCHEDULED)
            ->count();

        $assessmentsMonth = BodyAssessment::query()
            ->where('professional_id', $uid)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $activeWorkouts = TrainingPlan::query()
            ->where('professional_id', $uid)
            ->where('is_active', true)
            ->count();

        $patientIds = $professional->patients()->pluck('users.id');
        $unreadAlerts = HealthAlert::query()
            ->whereIn('user_id', $patientIds)
            ->where('is_read', false)
            ->count();

        return $this->success([
            'stats' => [
                'total_patients' => $totalPatients,
                'active_patients_30d' => $activePatients,
                'today_appointments' => $todayAppointments,
                'pending_appointments' => $pendingAppointments,
                'assessments_this_month' => $assessmentsMonth,
                'active_training_plans' => $activeWorkouts,
                'unread_alerts' => $unreadAlerts,
            ],
        ]);
    }
}
