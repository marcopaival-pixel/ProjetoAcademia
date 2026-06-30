<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\ProfessionalAppointment;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfessionalAppointmentController extends Controller
{
    use FormatsApiResponses;

    public function __construct(private AgendaService $agendaService) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
        ]);

        $query = ProfessionalAppointment::query()
            ->with('patient:id,name')
            ->where('professional_id', $request->user()->id)
            ->orderBy('appointment_at');

        if (! empty($validated['date'])) {
            $day = Carbon::parse($validated['date']);
            $query->whereBetween('appointment_at', [
                $day->copy()->startOfDay(),
                $day->copy()->endOfDay(),
            ]);
        }

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $appointments = $query->get()->map(fn (ProfessionalAppointment $item): array => $this->formatAppointment($item));

        return $this->success([
            'appointments' => $appointments,
        ], ['count' => $appointments->count()]);
    }

    public function updateStatus(Request $request, ProfessionalAppointment $appointment): JsonResponse
    {
        if ($appointment->professional_id !== $request->user()->id) {
            return $this->error('Sem permissão para alterar este agendamento.', 403, 'forbidden');
        }

        $validated = $request->validate([
            'status' => ['required', 'string'],
        ]);

        try {
            $updated = $this->agendaService->updateAppointmentStatus(
                $request->user(),
                $appointment,
                $validated['status']
            );
            $updated->load('patient:id,name');

            return $this->success($this->formatAppointment($updated));
        } catch (ValidationException $e) {
            return $this->error(
                collect($e->errors())->flatten()->first() ?? 'Dados inválidos.',
                422,
                'validation_error'
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function formatAppointment(ProfessionalAppointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'patient_name' => $appointment->patient?->name,
            'appointment_at' => $appointment->appointment_at?->toIso8601String(),
            'status' => $appointment->status,
            'status_label' => $appointment->status_label,
            'service_type' => $appointment->service_type,
            'notes' => $appointment->notes,
        ];
    }
}
