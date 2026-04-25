<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AgendaService;
use App\Models\ProfessionalAppointment;
use App\Models\ProfessionalAvailability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AgendaController extends Controller
{
    private AgendaService $agendaService;

    public function __construct(AgendaService $agendaService)
    {
        $this->agendaService = $agendaService;
    }

    public function index(Request $request, \App\Services\ReportMonetizationService $monetizationService)
    {
        $user = Auth::user();
        $isStudent = $user->hasRole('aluno');
        
        // Se for gestor, recepcionista ou admin, vê a agenda multi-profissional
        $isManager = $user->hasRole(['manager', 'supervisor', 'receptionist']) || $user->isAdministrator();

        if ($isManager) {
            $companyId = $user->academy_company_id;
            
            // Se for admin global e não tiver empresa, opcionalmente pegamos a primeira ou todas
            // Mas para o cenário de venda de clínica, o manager terá company_id.
            $professionals = \App\Models\User::whereHas('roles', function($q) {
                    $q->where('name', 'professional');
                })
                ->when($companyId, function($q) use ($companyId) {
                    $q->where('academy_company_id', $companyId);
                })
                ->with('professionalProfile')
                ->get();

            $date = $request->get('date', now()->toDateString());
            
            $appointments = ProfessionalAppointment::with(['patient', 'professional'])
                            ->whereIn('professional_id', $professionals->pluck('id'))
                            ->whereDate('appointment_at', $date)
                            ->get();

            return view('admin.agenda-multi', compact('professionals', 'appointments', 'date'));
        }

        if ($isStudent) {
            // Carrega profissionais vinculados para o aluno
            $linkedProfessionals = $user->professionals()->with('branding')->wherePivot('status', 'Sim')->get();

            // Branding dinâmico (usa o do profissional ativo ou o primeiro vinculado)
            $activeProfessional = $linkedProfessionals->where('id', session('active_professional_id'))->first() ?? $linkedProfessionals->first();
            $branding = $activeProfessional && $activeProfessional->branding 
                ? $activeProfessional->branding->toArray() 
                : [
                    'primary_color' => '#6366f1',
                    'accent_color' => '#a855f7',
                    'clinic_name' => 'NexShape',
                ];

            if ($linkedProfessionals->isEmpty() && !$user->isAdministrator()) {
                return view('patient.agenda', [
                    'appointments' => collect(),
                    'linkedProfessionals' => collect(),
                    'hasNoProfessionals' => true,
                    'branding' => $branding
                ]);
            }

            $query = ProfessionalAppointment::with('professional')
                            ->where('patient_id', $user->id)
                            ->orderBy('appointment_at', 'desc');

            if (!$monetizationService->hasPremium($user)) {
                $query->where('appointment_at', '>=', now()->subDays(30));
            }

            $appointments = $query->get();
            return view('patient.agenda', compact('appointments', 'linkedProfessionals', 'branding'));
        } else {
            // Se for profissional/instructor, vê a própria agenda
            $appointments = ProfessionalAppointment::with('patient')
                            ->where('professional_id', $user->id)
                            ->orderBy('appointment_at', 'desc')
                            ->get();
            
            $profile = $user->professionalProfile;
            $availabilities = ProfessionalAvailability::where('professional_id', $user->id)
                                ->orderBy('day_of_week')
                                ->get();

            return view('professional.agenda', compact('appointments', 'profile', 'availabilities'));
        }
    }

    public function updateStatus(Request $request, ProfessionalAppointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|string'
        ]);

        try {
            $this->agendaService->updateAppointmentStatus(Auth::user(), $appointment, $validated['status']);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSlots(Request $request)
    {
        $validated = $request->validate([
            'professional_id' => 'required|exists:users,id',
            'date' => 'required|date'
        ]);

        $slots = $this->agendaService->getAvailableSlots($validated['professional_id'], $validated['date']);
        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'professional_id' => 'required|exists:users,id',
            'patient_id' => 'nullable|exists:users,id',
            'appointment_at' => 'required|date',
            'service_type' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $appointment = $this->agendaService->scheduleAppointment(Auth::user(), $validated);
            return response()->json(['success' => true, 'appointment' => $appointment]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'appointment_duration' => 'required|integer|min:15|max:240',
            'appointment_interval' => 'required|integer|min:0|max:120',
            'availabilities' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $user->professionalProfile->update([
                'appointment_duration' => $validated['appointment_duration'],
                'appointment_interval' => $validated['appointment_interval'],
            ]);

            ProfessionalAvailability::where('professional_id', $user->id)->delete();
            foreach ($validated['availabilities'] as $dayNum => $data) {
                if (isset($data['enabled']) && $data['enabled'] == 'on') {
                    ProfessionalAvailability::create([
                        'professional_id' => $user->id,
                        'day_of_week' => $dayNum,
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Agenda configurada com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao salvar configurações.');
        }
    }

    public function cancel(Request $request, ProfessionalAppointment $appointment)
    {
        try {
            $this->agendaService->cancelAppointment(Auth::user(), $appointment);
            return response()->json(['success' => true, 'message' => 'Agendamento cancelado com sucesso.']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function waitlist(Request $request)
    {
        $validated = $request->validate([
            'professional_id' => 'nullable|exists:users,id',
            'date' => 'required|date'
        ]);

        try {
            $waitlist = $this->agendaService->addToWaitlist(Auth::user(), $validated['professional_id'] ?? null, $validated['date']);
            return response()->json(['success' => true, 'waitlist' => $waitlist]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
