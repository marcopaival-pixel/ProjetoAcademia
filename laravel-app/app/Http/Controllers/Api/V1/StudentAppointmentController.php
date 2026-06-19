<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\ProfessionalAppointment;
use App\Models\User;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentAppointmentController extends Controller
{
    use FormatsApiResponses;

    public function __construct(private AgendaService $agendaService) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = ProfessionalAppointment::query()
            ->with('professional:id,name')
            ->where('patient_id', $user->id)
            ->orderByDesc('appointment_at');

        if (! $user->hasPremiumAccess()) {
            $query->where('appointment_at', '>=', now()->subDays(30));
        }

        $appointments = $query->get()->map(fn (ProfessionalAppointment $item): array => $this->formatAppointment($item));

        return $this->success([
            'appointments' => $appointments,
        ], [
            'is_premium' => $user->hasPremiumAccess(),
            'count' => $appointments->count(),
        ]);
    }

    public function slots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
        ]);

        $user = $request->user();
        $professionalId = (int) $validated['professional_id'];

        $this->ensureProfessionalLink($user, $professionalId);

        $slots = $this->agendaService->getAvailableSlots($professionalId, $validated['date']);

        return $this->success([
            'date' => Carbon::parse($validated['date'])->toDateString(),
            'professional_id' => $professionalId,
            'slots' => $slots,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'integer', 'exists:users,id'],
            'appointment_at' => ['required', 'date'],
            'service_type' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $professionalId = (int) $validated['professional_id'];

        $this->ensureProfessionalLink($user, $professionalId, attachIfMissing: true);

        try {
            $appointment = $this->agendaService->scheduleAppointment($user, [
                'professional_id' => $professionalId,
                'patient_id' => $user->id,
                'appointment_at' => $validated['appointment_at'],
                'service_type' => $validated['service_type'] ?? 'Avaliação',
                'notes' => $validated['notes'] ?? null,
            ]);

            $appointment->load('professional:id,name');

            return $this->success($this->formatAppointment($appointment), status: 201);
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
            'professional_id' => $appointment->professional_id,
            'professional_name' => $appointment->professional?->name,
            'appointment_at' => $appointment->appointment_at?->toIso8601String(),
            'status' => $appointment->status,
            'status_label' => $appointment->status_label,
            'service_type' => $appointment->service_type,
            'notes' => $appointment->notes,
        ];
    }

    private function ensureProfessionalLink(User $user, int $professionalId, bool $attachIfMissing = false): void
    {
        $linked = $user->professionals()
            ->where('users.id', $professionalId)
            ->wherePivot('status', 'Sim')
            ->exists();

        if ($linked) {
            return;
        }

        if (! $attachIfMissing) {
            throw new AuthorizationException('Você não está vinculado a este profissional.');
        }

        $professional = User::query()->findOrFail($professionalId);

        if (! $professional->isProfessional()) {
            throw ValidationException::withMessages(['professional_id' => 'Profissional inválido.']);
        }

        $user->professionals()->syncWithoutDetaching([
            $professionalId => [
                'data_cadastro' => now(),
                'status' => 'Sim',
                'empresa_id' => $professional->academy_company_id,
            ],
        ]);
    }
}
