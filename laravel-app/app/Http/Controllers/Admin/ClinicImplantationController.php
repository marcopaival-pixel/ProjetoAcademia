<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyCompany;
use App\Models\ClinicOnboardingStep;
use App\Models\Especialidade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClinicImplantationController extends Controller
{
    private $steps = [
        1 => ['key' => 'registration', 'title' => 'Cadastro da clínica'],
        2 => ['key' => 'plan', 'title' => 'Seleção do plano'],
        3 => ['key' => 'initial_config', 'title' => 'Configuração visual'],
        4 => ['key' => 'users', 'title' => 'Equipe administrativa'],
        5 => ['key' => 'specialties', 'title' => 'Especialidades'],
        6 => ['key' => 'professionals', 'title' => 'Corpo clínico'],
        7 => ['key' => 'agenda', 'title' => 'Configuração da agenda'],
        8 => ['key' => 'patients', 'title' => 'Banco de pacientes'],
        9 => ['key' => 'medical_records', 'title' => 'Configuração do prontuário'],
        10 => ['key' => 'training', 'title' => 'Treinamento'],
        11 => ['key' => 'test', 'title' => 'Homologação'],
        12 => ['key' => 'activation', 'title' => 'Ativação final'],
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(AcademyCompany $company = null)
    {
        if (!$company) {
            $company = AcademyCompany::where('onboarding_status', '!=', 'completed')
                ->latest()
                ->first();

            if (!$company) {
                return redirect()->route('admin.pdf-companies.index')->with('info', 'Nenhuma clínica em processo de implantação.');
            }
        }

        return redirect()->route('admin.clinic-onboarding.step', [$company, $company->current_onboarding_step]);
    }

    public function showStep(AcademyCompany $company, $step)
    {
        $stepNumber = (int) $step;
        if (!isset($this->steps[$stepNumber])) {
            return redirect()->route('admin.clinic-onboarding.step', [$company, $company->current_onboarding_step]);
        }

        // Prevent skipping steps
        if ($stepNumber > $company->current_onboarding_step) {
             return redirect()->route('admin.clinic-onboarding.step', [$company, $company->current_onboarding_step])
                 ->with('warning', 'Por favor, complete a etapa atual antes de avançar.');
        }

        $stepData = $this->steps[$stepNumber];
        $viewData = $this->prepareStepData($company, $stepNumber);

        return view("admin.clinic-onboarding.step{$stepNumber}", array_merge([
            'company' => $company,
            'step' => $stepNumber,
            'steps' => $this->steps,
            'currentStep' => $stepData
        ], $viewData));
    }

    public function saveStep(Request $request, AcademyCompany $company, $step)
    {
        $stepNumber = (int) $step;
        $method = "saveStep{$stepNumber}";

        if (method_exists($this, $method)) {
            return $this->$method($request, $company);
        }

        return back()->with('error', 'Método de salvamento não implementado.');
    }

    private function prepareStepData(AcademyCompany $company, $step)
    {
        switch ($step) {
            case 2: // Plan
                return ['plans' => \App\Models\Plan::where('is_corporate', true)->where('status', 'active')->with('features')->get()];
            case 4: // Users
                $onboardingStep = $company->onboardingSteps()->where('step_key', 'users')->first();
                $selectedMuscleIds = $onboardingStep?->data['selected_muscles'] ?? [];
                
                return [
                    'users' => $company->users()->whereDoesntHave('roles', fn($q) => $q->where('name', 'professional'))->get(),
                    'selectedMuscles' => \App\Models\Muscle::with('group')->whereIn('id', $selectedMuscleIds)->get()->map(function($m) {
                        return [
                            'id' => $m->id,
                            'name' => $m->name,
                            'group' => $m->group->name,
                            'type' => $m->type
                        ];
                    })
                ];
            case 5: // Specialties
                return [
                    'allSpecialties' => Especialidade::active()->get(),
                    'selectedSpecialties' => $company->onboardingSteps()->where('step_key', 'specialties')->first()?->data['specialties'] ?? [],
                    'profissionais' => $company->users()->whereHas('roles', fn($q) => $q->where('name', 'professional'))->get(),
                    'professionals' => $company->users()->whereHas('roles', fn($q) => $q->where('name', 'professional'))->get(),
                ];
            case 6: // Professionals
                return ['professionals' => $company->users()->whereHas('roles', fn($q) => $q->where('name', 'professional'))->get()];
            case 8: // Patients
                return ['patientCount' => $company->users()->whereHas('roles', fn($q) => $q->where('name', 'paciente'))->count()];
            default:
                return [
                    'users' => collect(),
                    'professionals' => collect(),
                    'profissionais' => collect(),
                    'allSpecialties' => collect(),
                    'selectedSpecialties' => [],
                    'patientCount' => 0
                ];
        }
    }

    private function completeStep(AcademyCompany $company, $stepNumber, $data = null)
    {
        $stepKey = $this->steps[$stepNumber]['key'];

        ClinicOnboardingStep::updateOrCreate(
            ['academy_company_id' => $company->id, 'step_key' => $stepKey],
            ['is_completed' => true, 'completed_at' => now(), 'data' => $data]
        );

        if ($company->current_onboarding_step == $stepNumber && $stepNumber < 12) {
            $company->update(['current_onboarding_step' => $stepNumber + 1]);
        }

        if ($stepNumber == 12) {
            $company->update([
                'onboarding_status' => 'completed',
                'is_active' => true
            ]);
            return redirect()->route('admin.pdf-companies.index')->with('success', 'Clínica implantada e ativada com sucesso!');
        }

        return redirect()->route('admin.clinic-onboarding.step', [$company, $stepNumber + 1]);
    }

    // --- Step Savers ---

    private function saveStep1(Request $request, AcademyCompany $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'legal_name' => 'nullable|string|max:191',
            'tax_id' => 'required|string|max:64',
            'responsible_name' => 'required|string|max:120',
            'responsible_email' => 'required|email',
        ]);

        $company->update($validated);

        return $this->completeStep($company, 1);
    }

    private function saveStep2(Request $request, AcademyCompany $company)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);

        // Link plan to company via subscription
        \App\Models\Subscription::updateOrCreate(
            ['academy_company_id' => $company->id],
            [
                'plan_id' => $request->plan_id,
                'status' => 'active',
                'start_date' => now(),
            ]
        );

        return $this->completeStep($company, 2, ['plan_id' => $request->plan_id]);
    }

    private function saveStep3(Request $request, AcademyCompany $company)
    {
        $validated = $request->validate([
            'primary_color' => 'required|string|size:7',
            'accent_color' => 'required|string|size:7',
            'logo' => 'nullable|image|max:2048',
        ]);

        $company->update([
            'primary_color' => $validated['primary_color'],
            'accent_color' => $validated['accent_color'],
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $company->update(['logo_path' => $path]);
        }

        return $this->completeStep($company, 3);
    }

    private function saveStep4(Request $request, AcademyCompany $company)
    {
        if ($company->users()->count() == 0) {
            return back()->with('error', 'Cadastre ao menos um usuário administrativo.');
        }

        $muscles = $request->input('selected_muscles', '[]');
        $muscleIds = json_decode($muscles, true);

        return $this->completeStep($company, 4, ['selected_muscles' => $muscleIds]);
    }

    private function saveStep5(Request $request, AcademyCompany $company)
    {
        $validated = $request->validate([
            'specialties' => 'required|array',
            'specialties.*' => 'exists:especialidades,id',
        ]);

        return $this->completeStep($company, 5, ['specialties' => $validated['specialties']]);
    }

    private function saveStep6(Request $request, AcademyCompany $company)
    {
        if ($company->users()->whereHas('roles', fn($q) => $q->where('name', 'professional'))->count() == 0) {
            return back()->with('warning', 'Nenhum profissional cadastrado ainda. Deseja prosseguir assim mesmo?')->withInput();
        }

        return $this->completeStep($company, 6);
    }

    private function saveStep7(Request $request, AcademyCompany $company)
    {
        return $this->completeStep($company, 7);
    }

    private function saveStep8(Request $request, AcademyCompany $company)
    {
        return $this->completeStep($company, 8);
    }

    private function saveStep9(Request $request, AcademyCompany $company)
    {
        $company->update(['shared_medical_records' => $request->has('shared_medical_records')]);
        return $this->completeStep($company, 9);
    }

    private function saveStep10(Request $request, AcademyCompany $company)
    {
        return $this->completeStep($company, 10);
    }

    private function saveStep11(Request $request, AcademyCompany $company)
    {
        return $this->completeStep($company, 11);
    }

    private function saveStep12(Request $request, AcademyCompany $company)
    {
        return $this->completeStep($company, 12);
    }
}
