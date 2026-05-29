<?php

namespace App\Http\Controllers;

use App\Models\AcademyCompany;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PremiumOnboardingController extends Controller
{
    public function __construct(
        private readonly OnboardingService $onboardingService
    ) {}

    /**
     * Passo 1: Seleção do Tipo de Conta
     */
    public function index()
    {
        return view('onboarding.step1', ['currentStep' => 1]);
    }

    /**
     * Inicia o processo com o tipo de conta selecionado.
     */
    public function start(Request $request)
    {
        $request->validate([
            'account_type' => 'required|in:aluno,profissional,clinica,franquia'
        ]);

        $company = $this->onboardingService->start($request->account_type);
        
        Session::put('onboarding_company_uuid', $company->uuid);

        return redirect()->route('onboarding-premium.step', 2);
    }

    /**
     * Exibe um passo específico.
     */
    public function showStep($step)
    {
        $uuid = Session::get('onboarding_company_uuid');
        
        if (!$uuid && $step > 1) {
            return redirect()->route('onboarding-premium.index');
        }

        $company = $uuid ? AcademyCompany::where('uuid', $uuid)->firstOrFail() : null;

        if ($company && $step > $company->current_onboarding_step) {
            return redirect()->route('onboarding-premium.step', $company->current_onboarding_step);
        }

        return view("onboarding.step{$step}", [
            'company' => $company,
            'currentStep' => $step,
        ]);
    }

    /**
     * Salva os dados de um passo.
     */
    public function saveStep(Request $request, $step)
    {
        $uuid = Session::get('onboarding_company_uuid');
        $company = AcademyCompany::where('uuid', $uuid)->firstOrFail();

        $data = $request->except(['_token']);
        
        // Validações específicas por passo poderiam ser adicionadas aqui ou no Service
        
        $this->onboardingService->saveStep($company, (int)$step, $data);

        if ($step == 7) {
            return redirect()->route('onboarding-premium.finish');
        }

        return redirect()->route('onboarding-premium.step', $step + 1);
    }

    /**
     * Finalização do onboarding.
     */
    public function finish()
    {
        $uuid = Session::get('onboarding_company_uuid');
        $company = AcademyCompany::where('uuid', $uuid)->firstOrFail();

        return view('onboarding.finish', [
            'company' => $company,
            'currentStep' => 7
        ]);
    }
}
