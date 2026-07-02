<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\AcademyCompany;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClinicManagementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = AcademyCompany::query()->find($user->academy_company_id);

        if (! $company) {
            return back()->with('error', 'Você não está vinculado a uma empresa/conta.');
        }

        // Listar todas as clínicas vinculadas a esta empresa
        $clinics = Clinic::where('academy_company_id', $company->id)->get();

        // Se não houver nenhuma clínica, criar a primeira baseada nos dados da empresa (Auto-migração)
        if ($clinics->isEmpty()) {
            $clinic = Clinic::create([
                'academy_company_id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug ?? Str::slug($company->name),
                'logo_path' => $company->logo_path,
                'primary_color' => $company->primary_color ?? '#10b981',
                'is_active' => true,
            ]);
            $clinics = collect([$clinic]);

            // Vincular o usuário atual a esta clínica se ele não tiver uma
            if (!$user->clinic_id) {
                $user->update(['clinic_id' => $clinic->id]);
            }
        }

        $team = User::where('academy_company_id', $company->id)
            ->with(['roles', 'clinic'])
            ->get();

        $inviteUrl = route('register', ['company_slug' => $company->slug, 'tipo_acesso' => 'professional']);

        return view('clinic.settings', compact('company', 'clinics', 'team', 'inviteUrl'));
    }

    public function storeClinic(Request $request)
    {
        $user = Auth::user();
        $company = AcademyCompany::query()->find($user->academy_company_id);

        if (! $company) {
            return back()->with('error', 'Você não está vinculado a uma empresa/conta.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:clinics,slug',
            'primary_color' => 'required|string|size:7',
        ]);

        Clinic::create([
            'academy_company_id' => $company->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'primary_color' => $validated['primary_color'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Nova clínica adicionada com sucesso.');
    }

    public function updateBranding(Request $request)
    {
        $user = Auth::user();
        $clinicId = $request->input('clinic_id') ?? $user->clinic_id;
        $clinic = Clinic::findOrFail($clinicId);

        // Verificar se a clínica pertence à empresa do usuário
        if ($clinic->academy_company_id !== $user->academy_company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'primary_color' => 'required|string|size:7',
            'logo' => 'nullable|image|max:2048',
            'enabled_modules' => 'nullable|array',
        ]);

        $clinic->update([
            'name' => $validated['name'],
            'primary_color' => $validated['primary_color'],
            'enabled_modules' => $validated['enabled_modules'] ?? [],
        ]);

        if ($request->hasFile('logo')) {
            if ($clinic->logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($clinic->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $clinic->update(['logo_path' => $path]);
        }

        return back()->with('success', 'Configurações da clínica atualizadas.');
    }
}
