<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalAppointment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $context = $request->attributes->get('active_patient_context') ?? $request->attributes->get('active_patient_link');
        $professionalId = $context->professional_id;

        $appointments = ProfessionalAppointment::with('professional')
            ->where('patient_id', $request->user()->id)
            ->where('professional_id', $professionalId)
            ->orderByDesc('appointment_at')
            ->limit(50)
            ->get();

        return response()->json([
            'data' => [
                'appointments' => $appointments->map(fn (ProfessionalAppointment $appointment) => $this->payload($appointment))->values(),
            ],
        ]);
    }

    public function slots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
        ]);
        
        $context = $request->attributes->get('active_patient_context') ?? $request->attributes->get('active_patient_link');
        $professionalId = $context->professional_id;

        $date = Carbon::parse($validated['date'])->toDateString();
        $taken = ProfessionalAppointment::where('professional_id', $professionalId)
            ->whereDate('appointment_at', $date)
            ->pluck('appointment_at')
            ->map(fn ($value) => Carbon::parse($value)->format('H:i'))
            ->all();

        $slots = collect(['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'])
            ->map(fn (string $time) => ['time' => $time, 'available' => ! in_array($time, $taken, true)])
            ->values();

        return response()->json([
            'data' => [
                'date' => $date,
                'professional_id' => $professionalId,
                'slots' => $slots,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'appointment_at' => ['required', 'date'],
            'service_type' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $context = $request->attributes->get('active_patient_context') ?? $request->attributes->get('active_patient_link');
        $professionalId = $context->professional_id;

        $appointment = ProfessionalAppointment::create([
            'professional_id' => $professionalId,
            'patient_id' => $request->user()->id,
            'appointment_at' => $validated['appointment_at'],
            'status' => ProfessionalAppointment::STATUS_SCHEDULED,
            'service_type' => $validated['service_type'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $appointment->load('professional');

        return response()->json(['data' => $this->payload($appointment)], 201);
    }

    private function payload(ProfessionalAppointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'professional_id' => (int) $appointment->professional_id,
            'professional_name' => $appointment->professional?->name,
            'appointment_at' => optional($appointment->appointment_at)->toISOString(),
            'status' => $appointment->status,
            'status_label' => $appointment->status_label ?? $appointment->status,
            'service_type' => $appointment->service_type,
            'notes' => $appointment->notes,
        ];
    }
}
