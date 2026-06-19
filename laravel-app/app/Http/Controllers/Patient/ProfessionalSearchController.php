<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalPatientRequest;
use App\Models\User;
use App\Models\ProfessionalProfile;
use App\Models\UserProfile;
use App\Models\Especialidade;
use App\Models\ProfessionalAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfessionalSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereHas('roles', function($q) {
                $q->where('name', 'professional');
            })
            ->whereHas('professionalProfile', function($q) {
                $q->where('is_public', true);
            })
            ->with(['professionalProfile.profession', 'profile']);

        // Filtro por Nome/Email
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Filtro por Especialidade
        if ($request->filled('specialty')) {
            $query->whereHas('professionalProfile', function($sub) use ($request) {
                $sub->where('specialty', 'like', "%{$request->specialty}%");
            });
        }

        // Filtro por Tipo de Atendimento
        if ($request->filled('service_type')) {
            $query->whereHas('professionalProfile', function($sub) use ($request) {
                $sub->whereJsonContains('service_types', $request->service_type);
            });
        }

        // Filtro por Cidade
        if ($request->filled('city')) {
            $query->whereHas('profile', function($sub) use ($request) {
                $sub->where('city', 'like', "%{$request->city}%");
            });
        }

        // Filtro por Disponibilidade (Simplificado: profissionais que tem horários cadastrados)
        if ($request->has('available')) {
            $query->whereHas('availabilities');
        }

        $professionals = $query->paginate(12)->withQueryString();

        $specialties = Especialidade::active()->get();
        $cities = UserProfile::whereNotNull('city')->distinct()->pluck('city');

        return view('patient.professionals.search', compact('professionals', 'specialties', 'cities'));
    }

    public function show(User $professional)
    {
        $professional->load(['professionalProfile.profession', 'profile', 'availabilities']);
        return view('patient.professionals.show', compact('professional'));
    }

    public function schedule(Request $request, User $professional, \App\Services\AgendaService $agendaService)
    {
        $patient = auth()->user();

        if (strtolower($patient->profile->name) === 'aluno') {
            abort(403, 'Acesso Negado: Pacientes não têm permissão para agendar consultas diretamente via portal.');
        }

        $request->validate([
            'appointment_at' => 'required|date',
            'service_type' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // 1. Criar vínculo se não existir (Multi-vínculo permitido)
            if (!$patient->professionals()->where('profissional_id', $professional->id)->exists()) {
                $patient->professionals()->attach($professional->id, [
                    'data_cadastro' => now(),
                    'status' => 'Sim', // Ativo no sistema
                    'empresa_id' => $professional->academy_company_id,
                ]);
            }

            // 2. Registrar o atendimento via Service (Garante regras de negócio)
            $appointment = $agendaService->scheduleAppointment($patient, [
                'professional_id' => $professional->id,
                'appointment_at' => $request->appointment_at,
                'service_type' => $request->service_type,
                'notes' => $request->notes,
            ]);

            // 3. Notificar o profissional
            $professional->notify(new \App\Notifications\NewAppointmentNotification($appointment));

            return redirect()->route('patient.agenda')->with('success', 'Atendimento agendado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao agendar: ' . $e->getMessage())->withInput();
        }
    }

    public function requestLink(Request $request)
    {
        $request->validate([
            'professional_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        $patientId = auth()->id();
        $professionalId = $request->professional_id;

        // Verificar se já existe vínculo
        if (auth()->user()->professionals()->where('users.id', $professionalId)->exists()) {
            return back()->with('error', 'Você já está vinculado a este profissional.');
        }

        // Verificar se já existe solicitação pendente
        if (ProfessionalPatientRequest::where('patient_id', $patientId)
            ->where('professional_id', $professionalId)
            ->where('status', 'pending')
            ->exists()) {
            return back()->with('error', 'Você já possui uma solicitação pendente para este profissional.');
        }

        ProfessionalPatientRequest::create([
            'patient_id' => $patientId,
            'professional_id' => $professionalId,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        return redirect()->route('patient.professionals.search')->with('success', 'Solicitação de vínculo enviada com sucesso!');
    }
}
