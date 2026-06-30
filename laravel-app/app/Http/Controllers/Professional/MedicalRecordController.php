<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PatientAccessGuard;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\MedicalEvolution;
use App\Models\MedicalReport;
use App\Models\MedicalPrescription;
use App\Models\MedicalCertificate;
use App\Models\MedicalHistory;
use App\Models\PatientDocument;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Services\DompdfPdfService;

class MedicalRecordController extends Controller
{
    /**
     * Dashboard do Prontuário / Laudos
     */
    public function index(User $patient): View
    {
        $this->checkLink($patient);

        $patient->load(['profile', 'patients' => function($q) {
            $q->where('profissional_id', auth()->id());
        }]);

        $pivot = $patient->patients->first()->pivot;

        return view('professional.medical-records.index', [
            'patient' => $patient,
            'pivot' => $pivot,
        ]);
    }

    /**
     * Resumo do Paciente
     */
    public function summary(User $patient): View
    {
        $this->checkLink($patient);

        $patient->load(['profile', 'medicalEvolutions' => function($q) {
            $q->latest('date')->first();
        }]);

        $lastEvolution = $patient->medicalEvolutions->first();
        $pivot = auth()->user()->patients()->wherePivot('user_id', $patient->id)->first()->pivot;

        return view('professional.medical-records.summary', [
            'patient' => $patient,
            'profile' => $patient->profile,
            'lastEvolution' => $lastEvolution,
            'pivot' => $pivot,
        ]);
    }

    /**
     * Atualiza o resumo clínico (Diagnóstico e Notas)
     */
    public function updateSummary(Request $request, User $patient)
    {
        $this->checkLink($patient);

        $validated = $request->validate([
            'main_diagnosis' => 'nullable|string',
            'important_notes' => 'nullable|string',
        ]);

        auth()->user()->patients()->updateExistingPivot($patient->id, [
            'main_diagnosis' => $validated['main_diagnosis'],
            'important_notes' => $validated['important_notes'],
        ]);

        MedicalHistory::log($patient->id, 'update', 'summary', "Atualizou o resumo clínico (diagnóstico/notas)");

        return back()->with('success', 'Dados do resumo atualizados com sucesso.');
    }

    /**
     * Evolução / Atendimentos
     */
    public function evolutions(Request $request, User $patient): View
    {
        $this->checkLink($patient);

        $evolutions = $patient->medicalEvolutions()
            ->when($request->date, fn($q) => $q->whereDate('date', $request->date))
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.evolutions.index', compact('patient', 'evolutions'));
    }

    public function storeEvolution(Request $request, User $patient)
    {
        $this->checkLink($patient);

        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'nullable|string',
            'chief_complaint' => 'nullable|string',
            'assessment' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'conduct' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $evolution = $patient->medicalEvolutions()->create(array_merge($validated, [
            'professional_id' => auth()->id(),
        ]));

        MedicalHistory::log($patient->id, 'create', 'evolution', "Registrou novo atendimento/evolução em {$evolution->date->format('d/m/Y')}");

        return back()->with('success', 'Evolução registrada com sucesso.');
    }

    /**
     * Laudos
     */
    public function reports(Request $request, User $patient): View
    {
        $this->checkLink($patient);

        $reports = $patient->medicalReports()
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.reports.index', compact('patient', 'reports'));
    }

    public function storeReport(Request $request, User $patient)
    {
        $this->checkLink($patient);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $report = $patient->medicalReports()->create(array_merge($validated, [
            'professional_id' => auth()->id(),
        ]));

        MedicalHistory::log($patient->id, 'create', 'report', "Criou laudo: {$report->title}");

        return back()->with('success', 'Laudo criado com sucesso.');
    }

    /**
     * Receitas
     */
    public function prescriptions(Request $request, User $patient): View
    {
        $this->checkLink($patient);

        $prescriptions = $patient->medicalPrescriptions()
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.prescriptions.index', compact('patient', 'prescriptions'));
    }

    public function storePrescription(Request $request, User $patient)
    {
        $this->checkLink($patient);

        $validated = $request->validate([
            'medicine' => 'required|string|max:255',
            'date' => 'required|date',
            'dosage' => 'nullable|string',
            'frequency' => 'nullable|string',
            'duration' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $prescription = $patient->medicalPrescriptions()->create(array_merge($validated, [
            'professional_id' => auth()->id(),
        ]));

        MedicalHistory::log($patient->id, 'create', 'prescription', "Criou receita: {$prescription->medicine}");

        return back()->with('success', 'Receita criada com sucesso.');
    }

    /**
     * Atestados
     */
    public function certificates(Request $request, User $patient): View
    {
        $this->checkLink($patient);

        $certificates = $patient->medicalCertificates()
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.certificates.index', compact('patient', 'certificates'));
    }

    public function storeCertificate(Request $request, User $patient)
    {
        $this->checkLink($patient);

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'period' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $certificate = $patient->medicalCertificates()->create(array_merge($validated, [
            'professional_id' => auth()->id(),
        ]));

        MedicalHistory::log($patient->id, 'create', 'certificate', "Criou atestado: {$certificate->reason}");

        return back()->with('success', 'Atestado criado com sucesso.');
    }

    /**
     * Exames / Documentos
     */
    public function documents(Request $request, User $patient): View
    {
        $this->checkLink($patient);

        $documents = $patient->patientDocuments()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.documents.index', compact('patient', 'documents'));
    }

    /**
     * Histórico
     */
    public function history(Request $request, User $patient): View
    {
        $this->checkLink($patient);

        $histories = $patient->medicalHistories()
            ->with('user')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.history', compact('patient', 'histories'));
    }

    /**
     * Download de Laudo em PDF
     */
    public function downloadReport(User $patient, MedicalReport $report, DompdfPdfService $pdfService)
    {
        $this->authorize('view', $report);

        if ((int) $report->patient_id !== (int) $patient->id) {
            abort(403, 'Laudo não pertence a este paciente.');
        }

        $html = view('professional.medical-records.reports.pdf', compact('patient', 'report'))->render();
        return $pdfService->generate($html, "laudo-{$report->id}.pdf");
    }

    /**
     * Download de Atestado em PDF
     */
    public function downloadCertificate(User $patient, MedicalCertificate $certificate, DompdfPdfService $pdfService)
    {
        $this->authorize('view', $certificate);

        if ((int) $certificate->patient_id !== (int) $patient->id) {
            abort(403, 'Atestado não pertence a este paciente.');
        }

        $html = view('professional.medical-records.certificates.pdf', compact('patient', 'certificate'))->render();
        return $pdfService->generate($html, "atestado-{$certificate->id}.pdf");
    }

    /**
     * Retorna histórico de prescrições em JSON (para o AI Wizard)
     */
    public function prescriptionsJson(User $patient)
    {
        $this->checkLink($patient);

        $prescriptions = $patient->medicalPrescriptions()
            ->with('specialty')
            ->latest('date')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'date_formatted' => $p->date->format('d/m/Y'),
                    'medicine' => $p->medicine,
                    'dosage' => $p->dosage,
                    'frequency' => $p->frequency,
                    'specialty' => $p->specialty?->nome ?? 'Geral',
                ];
            });

        return response()->json($prescriptions);
    }

    /**
     * Verifica se o profissional tem vínculo com o paciente
     */
    private function checkLink(User $patient): void
    {
        try {
            PatientAccessGuard::assertProfessionalPatientLink(auth()->user(), $patient);
        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        }
    }
}


