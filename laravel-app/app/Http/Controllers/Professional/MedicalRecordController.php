<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PatientAccessGuard;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\MedicalEvolution;
use App\Models\MealTemplate;
use App\Models\MealTemplateItem;
use App\Models\MedicalReport;
use App\Models\MedicalPrescription;
use App\Models\MedicalCertificate;
use App\Models\MedicalHistory;
use App\Models\PatientDocument;
use App\Models\AdminLog;
use App\Models\BodyAssessment;
use App\Models\ClinicProtocol;
use App\Models\EvolutionPhoto;
use App\Models\PainRecord;
use App\Models\PatientTreatmentPlan;
use App\Services\MedicalRecordModuleManager;
use App\Services\SecureFileService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\DompdfPdfService;

class MedicalRecordController extends Controller
{
    public function __construct(private readonly MedicalRecordModuleManager $moduleManager)
    {
    }

    /**
     * Dashboard do Prontuário / Laudos
     */
    public function index(User $patient): View
    {
        $this->checkLink($patient);

        $patient->load(['profile', 'clinic', 'patients' => function($q) {
            $q->where('profissional_id', auth()->id());
        }]);

        $pivot = $patient->patients->first()->pivot;
        $medicalRecordModules = $this->moduleManager->forProfessional(auth()->user(), $patient);
        $medicalRecordMenuItems = $this->moduleManager->navigationForProfessional(auth()->user(), $patient);

        return view('professional.medical-records.index', [
            'patient' => $patient,
            'pivot' => $pivot,
            'medicalRecordModules' => $medicalRecordModules,
            'medicalRecordMenuItems' => $medicalRecordMenuItems,
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
        $this->ensureRestrictedModuleAccess($patient, 'session_notes', logRead: true);

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
        $this->ensureRestrictedModuleAccess($patient, 'session_notes');

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
     * Registros restritos de psicologia
     */
    public function sessionNotes(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureRestrictedModuleAccess($patient, 'session_notes', logRead: true);

        $sessionNotes = $patient->medicalEvolutions()
            ->where('professional_id', auth()->id())
            ->where('type', 'Registro de sessao')
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.session-notes.index', compact('patient', 'sessionNotes'));
    }

    public function storeSessionNote(Request $request, User $patient)
    {
        $this->checkLink($patient);
        $this->ensureRestrictedModuleAccess($patient, 'session_notes');

        $validated = $request->validate([
            'date' => 'required|date',
            'chief_complaint' => 'nullable|string|max:5000',
            'assessment' => 'nullable|string|max:10000',
            'conduct' => 'nullable|string|max:10000',
            'observations' => 'nullable|string|max:10000',
        ]);

        $patient->medicalEvolutions()->create(array_merge($validated, [
            'professional_id' => auth()->id(),
            'type' => 'Registro de sessao',
        ]));

        MedicalHistory::log($patient->id, 'create', 'session_note', 'Registrou nota sensível de sessão.');

        return back()->with('success', 'Registro de sessão salvo com sucesso.');
    }

    /**
     * Laudos
     */
    public function reports(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'reports');

        $reports = $patient->medicalReports()
            ->where('professional_id', auth()->id())
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.reports.index', compact('patient', 'reports'));
    }

    public function storeReport(Request $request, User $patient)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'reports');

        $validated = $this->validatedReportData($request);

        $report = $patient->medicalReports()->create(array_merge($validated, [
            'professional_id' => auth()->id(),
        ]));

        MedicalHistory::log($patient->id, 'create', 'report', "Criou laudo: {$report->title}");

        return back()->with('success', 'Laudo criado com sucesso.');
    }

    public function updateReport(Request $request, User $patient, MedicalReport $report)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'reports');
        $this->authorize('update', $report);
        $this->assertPatientRecord($patient, $report, 'patient_id');
        $this->assertProfessionalRecord($report);

        $report->update($this->validatedReportData($request));
        MedicalHistory::log($patient->id, 'update', 'report', "Atualizou laudo: {$report->title}");

        return back()->with('success', 'Laudo atualizado com sucesso.');
    }

    public function destroyReport(User $patient, MedicalReport $report)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'reports');
        $this->authorize('delete', $report);
        $this->assertPatientRecord($patient, $report, 'patient_id');
        $this->assertProfessionalRecord($report);

        $title = $report->title;
        $report->delete();
        MedicalHistory::log($patient->id, 'delete', 'report', "Removeu laudo: {$title}");

        return back()->with('success', 'Laudo removido com sucesso.');
    }

    /**
     * Receitas
     */
    public function prescriptions(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'prescriptions');

        $prescriptions = $patient->medicalPrescriptions()
            ->where('professional_id', auth()->id())
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.prescriptions.index', compact('patient', 'prescriptions'));
    }

    public function storePrescription(Request $request, User $patient)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'prescriptions');

        $validated = $this->validatedPrescriptionData($request);

        $prescription = $patient->medicalPrescriptions()->create(array_merge($validated, [
            'professional_id' => auth()->id(),
        ]));

        MedicalHistory::log($patient->id, 'create', 'prescription', "Criou receita: {$prescription->medicine}");

        return back()->with('success', 'Receita criada com sucesso.');
    }

    public function updatePrescription(Request $request, User $patient, MedicalPrescription $prescription)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'prescriptions');
        $this->authorize('update', $prescription);
        $this->assertPatientRecord($patient, $prescription, 'patient_id');
        $this->assertProfessionalRecord($prescription);

        $prescription->update($this->validatedPrescriptionData($request));
        MedicalHistory::log($patient->id, 'update', 'prescription', "Atualizou receita: {$prescription->medicine}");

        return back()->with('success', 'Receita atualizada com sucesso.');
    }

    public function destroyPrescription(User $patient, MedicalPrescription $prescription)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'prescriptions');
        $this->authorize('delete', $prescription);
        $this->assertPatientRecord($patient, $prescription, 'patient_id');
        $this->assertProfessionalRecord($prescription);

        $medicine = $prescription->medicine;
        $prescription->delete();
        MedicalHistory::log($patient->id, 'delete', 'prescription', "Removeu receita: {$medicine}");

        return back()->with('success', 'Receita removida com sucesso.');
    }

    /**
     * Atestados
     */
    public function certificates(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'certificates');

        $certificates = $patient->medicalCertificates()
            ->where('professional_id', auth()->id())
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.certificates.index', compact('patient', 'certificates'));
    }

    public function storeCertificate(Request $request, User $patient)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'certificates');

        $validated = $this->validatedCertificateData($request);

        $certificate = $patient->medicalCertificates()->create(array_merge($validated, [
            'professional_id' => auth()->id(),
        ]));

        MedicalHistory::log($patient->id, 'create', 'certificate', "Criou atestado: {$certificate->reason}");

        return back()->with('success', 'Atestado criado com sucesso.');
    }

    public function updateCertificate(Request $request, User $patient, MedicalCertificate $certificate)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'certificates');
        $this->authorize('update', $certificate);
        $this->assertPatientRecord($patient, $certificate, 'patient_id');
        $this->assertProfessionalRecord($certificate);

        $certificate->update($this->validatedCertificateData($request));
        MedicalHistory::log($patient->id, 'update', 'certificate', "Atualizou atestado: {$certificate->reason}");

        return back()->with('success', 'Atestado atualizado com sucesso.');
    }

    public function destroyCertificate(User $patient, MedicalCertificate $certificate)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'certificates');
        $this->authorize('delete', $certificate);
        $this->assertPatientRecord($patient, $certificate, 'patient_id');
        $this->assertProfessionalRecord($certificate);

        $reason = $certificate->reason;
        $certificate->delete();
        MedicalHistory::log($patient->id, 'delete', 'certificate', "Removeu atestado: {$reason}");

        return back()->with('success', 'Atestado removido com sucesso.');
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

    public function assessments(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'assessments');

        $assessments = BodyAssessment::withoutGlobalScopes()
            ->where('user_id', $patient->id)
            ->where(function ($query) {
                $query->where('professional_id', auth()->id())
                    ->orWhereNull('professional_id');
            })
            ->orderByDesc('assessment_date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.assessments.index', compact('patient', 'assessments'));
    }

    public function storeAssessment(Request $request, User $patient)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'assessments');

        $validated = $this->validatedAssessmentData($request);

        BodyAssessment::create(array_merge($validated, [
            'user_id' => $patient->id,
            'professional_id' => auth()->id(),
            'created_by' => 'professional',
            'status' => 'approved',
        ]));

        MedicalHistory::log($patient->id, 'create', 'assessment', 'Registrou nova avaliação física no prontuário.');

        return back()->with('success', 'Avaliação registrada com sucesso.');
    }

    public function updateAssessment(Request $request, User $patient, BodyAssessment $assessment)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'assessments');
        $this->assertPatientRecord($patient, $assessment, 'user_id');
        $this->assertProfessionalRecord($assessment);

        $assessment->update($this->validatedAssessmentData($request));
        MedicalHistory::log($patient->id, 'update', 'assessment', 'Atualizou avaliação física no prontuário.');

        return back()->with('success', 'Avaliação atualizada com sucesso.');
    }

    public function destroyAssessment(User $patient, BodyAssessment $assessment)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'assessments');
        $this->assertPatientRecord($patient, $assessment, 'user_id');
        $this->assertProfessionalRecord($assessment);

        $assessment->delete();
        MedicalHistory::log($patient->id, 'delete', 'assessment', 'Removeu avaliação física do prontuário.');

        return back()->with('success', 'Avaliação removida com sucesso.');
    }

    public function photos(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'photos');

        $photos = EvolutionPhoto::withoutGlobalScopes()
            ->where('user_id', $patient->id)
            ->orderByDesc('registered_date')
            ->paginate(12)
            ->withQueryString();

        return view('professional.medical-records.photos.index', compact('patient', 'photos'));
    }

    public function storePhoto(Request $request, User $patient, SecureFileService $secureFiles)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'photos');

        $validated = $request->validate([
            'photo' => 'required|image|max:10240',
            'type' => 'required|in:front,side,back,custom',
            'registered_date' => 'required|date',
            'weight_kg' => 'nullable|numeric|min:20|max:500',
            'notes' => 'nullable|string|max:2000',
        ]);

        $path = $secureFiles->storeSensitiveFile($request->file('photo'), 'evolution');

        EvolutionPhoto::create([
            'user_id' => $patient->id,
            'photo_path' => $path,
            'type' => $validated['type'],
            'registered_date' => $validated['registered_date'],
            'weight_kg' => $validated['weight_kg'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        MedicalHistory::log($patient->id, 'create', 'evolution_photo', 'Adicionou foto de evolução ao prontuário.');

        return back()->with('success', 'Foto de evolução adicionada com sucesso.');
    }

    public function destroyPhoto(User $patient, EvolutionPhoto $photo, SecureFileService $secureFiles)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'photos');
        $this->assertPatientRecord($patient, $photo, 'user_id');

        $secureFiles->deleteFile($photo->photo_path);
        $photo->delete();
        MedicalHistory::log($patient->id, 'delete', 'evolution_photo', 'Removeu foto de evolução do prontuário.');

        return back()->with('success', 'Foto de evolução removida com sucesso.');
    }

    public function updatePhoto(Request $request, User $patient, EvolutionPhoto $photo)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'photos');
        $this->assertPatientRecord($patient, $photo, 'user_id');

        $photo->update($this->validatedPhotoMetadata($request));
        MedicalHistory::log($patient->id, 'update', 'evolution_photo', 'Atualizou dados da foto de evolução no prontuário.');

        return back()->with('success', 'Foto de evolução atualizada com sucesso.');
    }

    public function pain(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'pain_tracking');

        $painRecords = PainRecord::withoutGlobalScopes()
            ->where('user_id', $patient->id)
            ->where(function ($query) {
                $query->where('professional_id', auth()->id())
                    ->orWhereNull('professional_id');
            })
            ->orderByDesc('assessment_date')
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.pain.index', compact('patient', 'painRecords'));
    }

    public function storePain(Request $request, User $patient)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'pain_tracking');

        $validated = $this->validatedPainData($request);

        PainRecord::create([
            'user_id' => $patient->id,
            'professional_id' => auth()->id(),
            'pain_points' => [
                'region' => $validated['region'],
                'laterality' => $validated['laterality'] ?? null,
            ],
            'eva_level' => (int) $validated['eva_level'],
            'notes' => $validated['notes'] ?? null,
            'assessment_date' => $validated['assessment_date'],
        ]);

        MedicalHistory::log($patient->id, 'create', 'pain_record', 'Registrou diário de dor no prontuário.');

        return back()->with('success', 'Registro de dor adicionado com sucesso.');
    }

    public function updatePain(Request $request, User $patient, PainRecord $painRecord)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'pain_tracking');
        $this->assertPatientRecord($patient, $painRecord, 'user_id');
        $this->assertProfessionalRecord($painRecord);

        $validated = $this->validatedPainData($request);
        $painRecord->update([
            'pain_points' => [
                'region' => $validated['region'],
                'laterality' => $validated['laterality'] ?? null,
            ],
            'eva_level' => (int) $validated['eva_level'],
            'notes' => $validated['notes'] ?? null,
            'assessment_date' => $validated['assessment_date'],
        ]);

        MedicalHistory::log($patient->id, 'update', 'pain_record', 'Atualizou registro de dor no prontuário.');

        return back()->with('success', 'Registro de dor atualizado com sucesso.');
    }

    public function destroyPain(User $patient, PainRecord $painRecord)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'pain_tracking');
        $this->assertPatientRecord($patient, $painRecord, 'user_id');
        $this->assertProfessionalRecord($painRecord);

        $painRecord->delete();
        MedicalHistory::log($patient->id, 'delete', 'pain_record', 'Removeu registro de dor do prontuário.');

        return back()->with('success', 'Registro de dor removido com sucesso.');
    }

    public function protocols(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureAnyActiveModule($patient, ['therapeutic_exercises', 'aesthetic_protocols']);

        $type = $request->query('type');
        $protocols = ClinicProtocol::query()
            ->where('academy_company_id', auth()->user()->academy_company_id)
            ->when($type, fn ($query) => $query->where('type', $type))
            ->with('specialty')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('professional.medical-records.protocols.index', compact('patient', 'protocols', 'type'));
    }

    public function applyProtocol(User $patient, ClinicProtocol $protocol)
    {
        $this->checkLink($patient);
        $this->ensureAnyActiveModule($patient, ['therapeutic_exercises', 'aesthetic_protocols']);

        if ((int) $protocol->academy_company_id !== (int) auth()->user()->academy_company_id) {
            abort(403, 'Protocolo não pertence à clínica do profissional.');
        }

        $patient->medicalEvolutions()->create([
            'professional_id' => auth()->id(),
            'date' => now(),
            'type' => 'Protocolo aplicado',
            'chief_complaint' => $protocol->objective,
            'assessment' => $protocol->description,
            'conduct' => trim(($protocol->protocol ?? '')."\n\nFrequência: ".($protocol->frequency ?? '--')."\nDuração: ".($protocol->duration ?? '--')),
            'observations' => "Protocolo: {$protocol->name}",
        ]);

        MedicalHistory::log($patient->id, 'create', 'protocol', "Aplicou protocolo: {$protocol->name}");

        return back()->with('success', 'Protocolo aplicado e registrado na evolução clínica.');
    }

    public function nutrition(Request $request, User $patient): View
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'nutrition');

        $treatmentPlan = PatientTreatmentPlan::withoutGlobalScopes()
            ->where('patient_id', $patient->id)
            ->where('professional_id', auth()->id())
            ->latest()
            ->first();

        $mealTemplates = MealTemplate::withoutGlobalScopes()
            ->where('user_id', $patient->id)
            ->where(function ($query) {
                $query->where('professional_id', auth()->id())
                    ->orWhereNull('professional_id');
            })
            ->with('items')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('professional.medical-records.nutrition.index', compact('patient', 'treatmentPlan', 'mealTemplates'));
    }

    public function storeNutritionPlan(Request $request, User $patient)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'nutrition');

        $validated = $request->validate([
            'diagnosis' => 'nullable|string|max:5000',
            'objectives' => 'nullable|string|max:5000',
            'care_plan' => 'nullable|string|max:10000',
            'orientations' => 'nullable|string|max:10000',
            'is_active' => 'nullable|boolean',
        ]);

        PatientTreatmentPlan::updateOrCreate(
            [
                'patient_id' => $patient->id,
                'professional_id' => auth()->id(),
            ],
            array_merge($validated, [
                'is_active' => $request->boolean('is_active', true),
            ])
        );

        MedicalHistory::log($patient->id, 'update', 'nutrition_plan', 'Atualizou plano alimentar no prontuário.');

        return back()->with('success', 'Plano alimentar atualizado com sucesso.');
    }

    public function storeMealTemplate(Request $request, User $patient)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'nutrition');

        [$validated, $items] = $this->validatedMealTemplateData($request);

        $template = MealTemplate::create([
            'user_id' => $patient->id,
            'professional_id' => auth()->id(),
            'name' => $validated['name'],
        ]);

        foreach ($items as $index => $item) {
            MealTemplateItem::create([
                'meal_template_id' => $template->id,
                'meal_type' => $item['meal_type'],
                'food_name' => $item['food_name'],
                'calories' => $item['calories'] ?? 0,
                'protein_g' => $item['protein_g'] ?? 0,
                'carbs_g' => $item['carbs_g'] ?? 0,
                'fat_g' => $item['fat_g'] ?? 0,
                'position' => $index,
            ]);
        }

        MedicalHistory::log($patient->id, 'create', 'meal_template', "Criou modelo alimentar: {$template->name}");

        return back()->with('success', 'Modelo alimentar criado com sucesso.');
    }

    public function updateMealTemplate(Request $request, User $patient, MealTemplate $mealTemplate)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'nutrition');
        $this->assertPatientRecord($patient, $mealTemplate, 'user_id');
        $this->assertProfessionalRecord($mealTemplate);

        [$validated, $items] = $this->validatedMealTemplateData($request);

        $mealTemplate->update(['name' => $validated['name']]);
        $mealTemplate->items()->delete();

        foreach ($items as $index => $item) {
            MealTemplateItem::create([
                'meal_template_id' => $mealTemplate->id,
                'meal_type' => $item['meal_type'],
                'food_name' => $item['food_name'],
                'calories' => $item['calories'] ?? 0,
                'protein_g' => $item['protein_g'] ?? 0,
                'carbs_g' => $item['carbs_g'] ?? 0,
                'fat_g' => $item['fat_g'] ?? 0,
                'position' => $index,
            ]);
        }

        MedicalHistory::log($patient->id, 'update', 'meal_template', "Atualizou modelo alimentar: {$mealTemplate->name}");

        return back()->with('success', 'Modelo alimentar atualizado com sucesso.');
    }

    public function destroyMealTemplate(User $patient, MealTemplate $mealTemplate)
    {
        $this->checkLink($patient);
        $this->ensureActiveModule($patient, 'nutrition');
        $this->assertPatientRecord($patient, $mealTemplate, 'user_id');
        $this->assertProfessionalRecord($mealTemplate);

        $mealTemplate->items()->delete();
        $mealTemplate->delete();
        MedicalHistory::log($patient->id, 'delete', 'meal_template', "Removeu modelo alimentar: {$mealTemplate->name}");

        return back()->with('success', 'Modelo alimentar removido com sucesso.');
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

        $this->logMedicalDocumentDownload($patient, 'PROFESSIONAL_DOWNLOAD_MEDICAL_REPORT', 'medical_report', $report->id, [
            'professional_id' => auth()->id(),
            'owner_professional_id' => $report->professional_id,
        ]);

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

        $this->logMedicalDocumentDownload($patient, 'PROFESSIONAL_DOWNLOAD_MEDICAL_CERTIFICATE', 'medical_certificate', $certificate->id, [
            'professional_id' => auth()->id(),
            'owner_professional_id' => $certificate->professional_id,
        ]);

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

    private function validatedAssessmentData(Request $request): array
    {
        return $request->validate([
            'assessment_date' => 'required|date',
            'weight_kg' => 'nullable|numeric|min:20|max:500',
            'bf_percent' => 'nullable|numeric|min:1|max:70',
            'muscle_percent' => 'nullable|numeric|min:1|max:90',
            'waist' => 'nullable|numeric',
            'chest' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string|max:20',
            'heart_rate' => 'nullable|integer|min:20|max:240',
            'notes' => 'nullable|string|max:5000',
        ]);
    }

    private function validatedReportData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);
    }

    private function validatedPrescriptionData(Request $request): array
    {
        return $request->validate([
            'medicine' => 'required|string|max:255',
            'date' => 'required|date',
            'dosage' => 'nullable|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ]);
    }

    private function validatedCertificateData(Request $request): array
    {
        return $request->validate([
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'period' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ]);
    }

    private function validatedPainData(Request $request): array
    {
        return $request->validate([
            'assessment_date' => 'required|date',
            'eva_level' => 'required|integer|min:0|max:10',
            'region' => 'required|string|max:120',
            'laterality' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:3000',
        ]);
    }

    private function validatedPhotoMetadata(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:front,side,back,custom',
            'registered_date' => 'required|date',
            'weight_kg' => 'nullable|numeric|min:20|max:500',
            'notes' => 'nullable|string|max:2000',
        ]);
    }

    private function validatedMealTemplateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'items' => 'required|array|min:1|max:12',
            'items.*.meal_type' => 'required|string|max:32',
            'items.*.food_name' => 'nullable|string|max:200',
            'items.*.calories' => 'nullable|integer|min:0|max:5000',
            'items.*.protein_g' => 'nullable|numeric|min:0|max:500',
            'items.*.carbs_g' => 'nullable|numeric|min:0|max:500',
            'items.*.fat_g' => 'nullable|numeric|min:0|max:500',
        ]);

        $items = collect($validated['items'])
            ->filter(fn (array $item) => filled($item['food_name'] ?? null))
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Informe pelo menos um alimento no modelo.',
            ]);
        }

        return [$validated, $items];
    }

    private function assertPatientRecord(User $patient, object $record, string $patientColumn): void
    {
        if ((int) $record->{$patientColumn} !== (int) $patient->id) {
            abort(403, 'Registro não pertence a este paciente.');
        }
    }

    private function assertProfessionalRecord(object $record): void
    {
        if ((int) ($record->professional_id ?? 0) !== (int) auth()->id()) {
            abort(403, 'Registro não pertence ao profissional logado.');
        }
    }

    private function ensureActiveModule(User $patient, string $moduleKey): void
    {
        $module = collect($this->moduleManager->forProfessional(auth()->user(), $patient))
            ->firstWhere('key', $moduleKey);

        if (! $module || ($module['status'] ?? null) !== 'active') {
            abort(404, 'Módulo não disponível para este profissional ou clínica.');
        }
    }

    private function ensureAnyActiveModule(User $patient, array $moduleKeys): void
    {
        $modules = collect($this->moduleManager->forProfessional(auth()->user(), $patient));

        $hasActiveModule = collect($moduleKeys)->contains(function (string $moduleKey) use ($modules): bool {
            $module = $modules->firstWhere('key', $moduleKey);

            return $module && ($module['status'] ?? null) === 'active';
        });

        if (! $hasActiveModule) {
            abort(404, 'Módulo não disponível para este profissional ou clínica.');
        }
    }

    private function ensureRestrictedModuleAccess(User $patient, string $moduleKey, bool $logRead = false): void
    {
        $professional = auth()->user();
        $module = collect($this->moduleManager->forProfessional($professional, $patient))->firstWhere('key', $moduleKey);

        if (! $module || ($module['status'] ?? null) !== 'restricted') {
            return;
        }

        if (! ($module['can_access'] ?? false)) {
            abort(403, 'Acesso bloqueado: o paciente ainda não autorizou este tipo de dado sensível.');
        }

        if ($logRead) {
            AdminLog::create([
                'user_id' => $professional->id,
                'action' => 'ACCESS_VIEW_RESTRICTED_MEDICAL_RECORD',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'payload' => [
                    'patient_id' => $patient->id,
                    'module_key' => $moduleKey,
                    'data_type' => $module['data_type'] ?? null,
                    'timestamp' => now()->toDateTimeString(),
                ],
                'created_at' => now(),
            ]);
        }
    }

    private function logMedicalDocumentDownload(User $patient, string $action, string $documentType, int $documentId, array $extra = []): void
    {
        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => array_merge([
                'patient_id' => $patient->id,
                'document_type' => $documentType,
                'document_id' => $documentId,
                'timestamp' => now()->toDateTimeString(),
            ], $extra),
            'created_at' => now(),
        ]);
    }
}
