<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppointmentWaitlist;
use App\Models\ProfessionalAppointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $appointments = ProfessionalAppointment::with('professional')
            ->where('patient_id', $request->user()->id)
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
            'professional_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
        ]);

        $date = Carbon::parse($validated['date'])->toDateString();
        $taken = ProfessionalAppointment::where('professional_id', $validated['professional_id'])
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
                'professional_id' => (int) $validated['professional_id'],
                'slots' => $slots,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'integer', 'exists:users,id'],
            'appointment_at' => ['required', 'date'],
            'service_type' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $appointment = ProfessionalAppointment::create([
            'professional_id' => $validated['professional_id'],
            'patient_id' => $request->user()->id,
            'appointment_at' => $validated['appointment_at'],
            'status' => ProfessionalAppointment::STATUS_SCHEDULED,
            'service_type' => $validated['service_type'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $appointment->load('professional');

        return response()->json(['data' => $this->payload($appointment)], 201);
    }

    public function waitlist(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
        ]);

        $waitlist = AppointmentWaitlist::create([
            'professional_id' => $validated['professional_id'],
            'patient_id' => $request->user()->id,
            'requested_date' => Carbon::parse($validated['date'])->toDateString(),
            'status' => 'waiting',
        ]);

        return response()->json([
            'data' => [
                'id' => $waitlist->id,
                'professional_id' => (int) $waitlist->professional_id,
                'requested_date' => $waitlist->requested_date,
                'status' => $waitlist->status,
            ],
        ], 201);
    }

    public function professionals(): JsonResponse
    {
        $professionals = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'professional'))
            ->orderBy('name')
            ->limit(100)
            ->get();

        return response()->json([
            'data' => [
                'professionals' => $professionals->map(fn (User $professional) => [
                    'id' => $professional->id,
                    'name' => $professional->name,
                    'email' => $professional->email,
                    'specialty' => $professional->specialty ?? null,
                    'service_types' => [],
                    'profession' => null,
                ])->values(),
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => $professionals->count(),
                ],
            ],
        ]);
    }

    private function payload(ProfessionalAppointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'professional_id' => (int) $appointment->professional_id,
            'professional_name' => $appointment->professional?->name,
            'appointment_at' => optional($appointment->appointment_at)->toISOString(),
            'status' => $appointment->status,
            'status_label' => $appointment->status_label,
            'service_type' => $appointment->service_type,
            'notes' => $appointment->notes,
        ];
    }
}
