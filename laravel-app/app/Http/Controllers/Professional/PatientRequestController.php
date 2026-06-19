<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use App\Models\ProfessionalPatientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientRequestController extends Controller
{
    public function index()
    {
        $requests = auth()->user()->receivedRequests()
            ->with('patient')
            ->where('status', 'pending')
            ->get();

        return view('professional.patient-requests.index', compact('requests'));
    }

    public function approve($id)
    {
        $request = ProfessionalPatientRequest::where('professional_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $professional = auth()->user();
        
        // Verificar limite do plano
        $currentPatientsCount = $professional->patients()->count();
        $maxPatients = $professional->professionalPlan ? $professional->professionalPlan->max_patients : 50; // Default 50 if no plan

        if ($maxPatients !== -1 && $currentPatientsCount >= $maxPatients) {
            return back()->with('error', 'Limite de pacientes atingido para o seu plano atual.');
        }

        DB::transaction(function() use ($request, $professional) {
            $request->update(['status' => 'approved']);

            // Se for transferência (Item 7), desativa outros vínculos anteriores
            if ($request->message === 'Transferência') {
                \App\Models\ProfessionalPatient::where('user_id', $request->patient_id)
                    ->where('professional_id', '!=', $professional->id)
                    ->update(['status' => 'Não']);
            }

            $professional->patients()->syncWithoutDetaching([
                $request->patient_id => [
                    'status' => 'Sim',
                    'data_cadastro' => now(),
                    'empresa_id' => $professional->academy_company_id
                ]
            ]);
            // Notificar paciente (Item 10)
            $request->patient->notify(new \App\Notifications\PatientProfessionalLinkNotification($professional->name, 'new'));
        });

        return back()->with('success', 'Solicitação aprovada e paciente vinculado.');
    }

    public function reject($id)
    {
        $request = ProfessionalPatientRequest::where('professional_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->update(['status' => 'rejected']);

        return back()->with('success', 'Solicitação rejeitada.');
    }

    public function approveMeasurement($id)
    {
        $measurement = BodyAssessment::where('professional_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $measurement->update(['status' => 'approved']);

        return back()->with('success', 'Avaliação física aprovada e integrada ao histórico.');
    }
}


