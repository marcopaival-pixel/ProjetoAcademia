<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FiscalSettingController extends Controller
{
    public function edit(): View
    {
        $this->authorize('admin.financial.management');

        return view('admin.fiscal-settings.edit', [
            'setting' => FiscalSetting::active() ?? new FiscalSetting([
                'provider' => 'fake',
                'environment' => 'sandbox',
                'is_active' => true,
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('admin.financial.management');

        $data = $request->validate([
            'provider' => ['required', 'string', 'in:fake'],
            'environment' => ['required', 'string', 'in:sandbox,production'],
            'issuer_cnpj' => ['required', 'string', 'max:20'],
            'issuer_legal_name' => ['required', 'string', 'max:255'],
            'municipal_registration' => ['required', 'string', 'max:100'],
            'tax_regime' => ['required', 'string', 'max:100'],
            'cnae' => ['required', 'string', 'max:32'],
            'municipal_service_code' => ['required', 'string', 'max:64'],
            'iss_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'certificate_reference' => ['nullable', 'string', 'max:255'],
            'api_token' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['issuer_cnpj'] = preg_replace('/\D+/', '', $data['issuer_cnpj']);
        $data['is_active'] = $request->boolean('is_active');

        $setting = FiscalSetting::query()->latest('id')->first();
        FiscalSetting::query()->update(['is_active' => false]);
        FiscalSetting::updateOrCreate(['id' => $setting?->id], $data);

        return redirect()->route('admin.financial.fiscal-settings.edit')
            ->with('success', 'Configuração fiscal guardada com sucesso.');
    }
}
