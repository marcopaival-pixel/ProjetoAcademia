<?php

namespace App\Http\Controllers\Api\V1\Professional;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalAppointment;
use App\Services\AgendaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(private AgendaService $agendaService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = ProfessionalAppointment::with('patient:id,name')
            ->where('professional_id', $request->user()->id);

        if ($date = $request->query('date')) {
            $query->whereDate('appointment_at', $date);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('appointment_at')->limit(100)->get()->map(fn ($a) => [
            'id' => $a->id,
            'patient_id' => $a->patient_id,
            'patient_name' => $a->patient?->name,
            'appointment_at' => $a->appointment_at?->toIso8601String(),
            'status' => $a->status,
            'service_type' => $a->service_type,
            'notes' => $a->notes,
        ]);

        return response()->json(['data' => ['appointments' => $appointments]]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $appointment = ProfessionalAppointment::query()
            ->where('professional_id', $request->user()->id)
            ->findOrFail($id);

        $this->agendaService->updateAppointmentStatus($request->user(), $appointment, $validated['status']);

        return response()->json([
            'data' => [
                'id' => $appointment->id,
                'status' => $appointment->fresh()->status,
            ],
        ]);
    }
}
