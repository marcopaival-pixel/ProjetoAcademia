<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyCompany;
use App\Models\AcademyUnit;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AcademyCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $u = $request->user();
            if ($u === null || (! $u->isAdministrator() && ! $u->hasPermission('pdf.companies.manage'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(): View
    {
        $companies = AcademyCompany::query()->withCount('units')->orderBy('name')->get();

        return view('admin.pdf-suite.companies-index', compact('companies'));
    }

    public function create(): View
    {
        return view('admin.pdf-suite.company-form', [
            'company' => new AcademyCompany,
            'mode' => 'create',
            'watermarkText' => '',
            'watermarkOpacity' => 0.12,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'legal_name' => ['nullable', 'string', 'max:191'],
            'tax_id' => ['required', 'string', 'max:64', 'unique:academy_companies,tax_id'],
            'responsible_name' => ['required', 'string', 'max:120'],
            'responsible_email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'size:2'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'watermark_text' => ['nullable', 'string', 'max:120'],
            'watermark_opacity' => ['nullable', 'numeric', 'between:0.02,1'],
        ]);

        $slug = Str::slug($data['name']);
        if ($slug === '') {
            $slug = 'empresa-'.Str::lower(Str::random(6));
        }
        $base = $slug;
        $i = 1;
        while (AcademyCompany::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $pdfSettings = [];
        if (! empty($data['watermark_text'])) {
            $pdfSettings['watermark'] = [
                'text' => $data['watermark_text'],
                'opacity' => (float) ($data['watermark_opacity'] ?? 0.12),
                'position' => 'diagonal',
            ];
        }

        try {
            DB::beginTransaction();

            $company = AcademyCompany::create([
                'name' => $data['name'],
                'slug' => $slug,
                'legal_name' => $data['legal_name'] ?? null,
                'tax_id' => $data['tax_id'],
                'responsible_name' => $data['responsible_name'],
                'responsible_email' => $data['responsible_email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'zip_code' => $data['zip_code'] ?? null,
                'pdf_settings' => $pdfSettings !== [] ? $pdfSettings : null,
                'is_active' => true,
                'onboarding_status' => 'in_progress',
                'current_onboarding_step' => 2, // Will skip to plan/config
            ]);

            // Create Auto-Admin User
            $user = User::create([
                'name' => $data['responsible_name'],
                'email' => $data['responsible_email'],
                'password_hash' => Hash::make(str_replace(['.', '-', '/'], '', $data['tax_id'])), // CNPJ as default pass
                'academy_company_id' => $company->id,
                'status' => 'active',
                'user_type' => 'ADMIN_CLINICA',
            ]);

            $user->assignRole('manager');

            DB::commit();

            return redirect()->route('admin.clinic-onboarding.index', $company)->with('success', 'Clínica e Administrador criados com sucesso! Vamos prosseguir com o onboarding.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar clínica: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(AcademyCompany $academyCompany): View
    {
        $academyCompany->load('units');
        $wm = $academyCompany->watermarkConfig();

        return view('admin.pdf-suite.company-form', [
            'company' => $academyCompany,
            'mode' => 'edit',
            'watermarkText' => $wm['text'] ?? '',
            'watermarkOpacity' => $wm['opacity'] ?? 0.12,
        ]);
    }

    public function update(Request $request, AcademyCompany $academyCompany): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'legal_name' => ['nullable', 'string', 'max:191'],
            'tax_id' => ['nullable', 'string', 'max:64'],
            'watermark_text' => ['nullable', 'string', 'max:120'],
            'watermark_opacity' => ['nullable', 'numeric', 'between:0.02,1'],
        ]);

        $pdfSettings = $academyCompany->pdf_settings ?? [];
        if (! is_array($pdfSettings)) {
            $pdfSettings = [];
        }
        if (! empty($data['watermark_text'])) {
            $pdfSettings['watermark'] = [
                'text' => $data['watermark_text'],
                'opacity' => (float) ($data['watermark_opacity'] ?? 0.12),
                'position' => 'diagonal',
            ];
        } else {
            unset($pdfSettings['watermark']);
        }

        $academyCompany->update([
            'name' => $data['name'],
            'legal_name' => $data['legal_name'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'pdf_settings' => $pdfSettings !== [] ? $pdfSettings : null,
        ]);

        return back()->with('success', 'Empresa atualizada.');
    }

    public function storeUnit(Request $request, AcademyCompany $academyCompany): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'code' => ['nullable', 'string', 'max:64'],
        ]);
        AcademyUnit::create([
            'academy_company_id' => $academyCompany->id,
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Unidade criada.');
    }
}
