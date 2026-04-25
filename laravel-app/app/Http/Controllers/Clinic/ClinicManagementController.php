<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\AcademyCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicManagementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->academyCompany;

        if (!$company) {
            return back()->with('error', 'Você não está vinculado a uma clínica.');
        }

        $team = User::where('academy_company_id', $company->id)
            ->with('roles')
            ->get();

        $inviteUrl = route('register', ['company_slug' => $company->slug, 'tipo_acesso' => 'professional']);

        return view('clinic.settings', compact('company', 'team', 'inviteUrl'));
    }

    public function updateBranding(Request $request)
    {
        $user = Auth::user();
        $company = $user->academyCompany;

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'primary_color' => 'required|string|size:7',
            'accent_color' => 'required|string|size:7',
            'logo' => 'nullable|image|max:2048',
            'shared_medical_records' => 'nullable|boolean',
        ]);

        $company->update([
            'name' => $validated['name'],
            'primary_color' => $validated['primary_color'],
            'accent_color' => $validated['accent_color'],
            'shared_medical_records' => $request->has('shared_medical_records'),
        ]);

        \Illuminate\Support\Facades\Cache::forget("company_shared_records_{$company->id}");

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($company->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $company->update(['logo_path' => $path]);
        }

        return back()->with('success', 'Configurações da clínica atualizadas.');
    }
}
