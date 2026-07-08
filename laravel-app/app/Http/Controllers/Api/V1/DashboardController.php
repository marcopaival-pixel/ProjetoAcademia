<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use App\Models\HealthAlert;
use App\Models\ProfessionalAppointment;
use App\Models\TrainingPlan;
use App\Services\Context\CurrentContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use FormatsApiResponses;

    protected CurrentContext $context;

    public function __construct(CurrentContext $context)
    {
        $this->context = $context;
    }

    /**
     * Dashboard unificado multi-role.
     */
    public function index(Request $request): JsonResponse
    {
        if (! $this->context->activeRole()) {
            return response()->json([
                'message' => 'Please select a profile to continue.',
                'require_role_selection' => true,
            ]);
        }

        if ($this->context->isAthlete()) {
            return $this->getAthleteDashboard();
        }

        if ($this->context->isProfessional()) {
            return $this->getProfessionalDashboard($request);
        }

        if ($this->context->isClinicAdmin()) {
            return $this->getClinicDashboard();
        }

        return response()->json([
            'message' => 'Dashboard indisponivel para este papel.',
            'role' => $this->context->activeRole(),
        ], 403);
    }

    private function getAthleteDashboard(): JsonResponse
    {
        return $this->success([
            'context' => 'athlete',
            'next_workouts' => [],
            'goals' => [],
        ]);
    }

    private function getProfessionalDashboard(Request $request): JsonResponse
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
        $patientIds = $professional->patients()->pluck('users.id');

        return $this->success([
            'stats' => [
                'total_patients' => $totalPatients,
                'active_patients_30d' => $activePatients,
                'today_appointments' => ProfessionalAppointment::query()
                    ->where('professional_id', $uid)
                    ->whereBetween('appointment_at', [$todayStart, $todayEnd])
                    ->count(),
                'pending_appointments' => ProfessionalAppointment::query()
                    ->where('professional_id', $uid)
                    ->where('appointment_at', '>=', $todayStart)
                    ->where('status', ProfessionalAppointment::STATUS_SCHEDULED)
                    ->count(),
                'assessments_this_month' => BodyAssessment::query()
                    ->where('professional_id', $uid)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'active_training_plans' => TrainingPlan::query()
                    ->where('professional_id', $uid)
                    ->where('is_active', true)
                    ->count(),
                'unread_alerts' => HealthAlert::query()
                    ->whereIn('user_id', $patientIds)
                    ->where('is_read', false)
                    ->count(),
            ],
        ]);
    }

    private function getClinicDashboard(): JsonResponse
    {
        return $this->success([
            'context' => 'clinic_admin',
            'professionals_count' => 5,
            'monthly_revenue' => 15000.00,
        ]);
    }
}
