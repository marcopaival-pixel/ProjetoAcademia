<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClinicSelectionController extends Controller
{
    /**
     * Exibe a tela de seleção de clínica para usuários com múltiplos vínculos.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        
        $clinics = \App\Models\Clinic::where('academy_company_id', $user->academy_company_id)
            ->where('is_active', true)
            ->get();

        if ($clinics->isEmpty()) {
             abort(403, 'Você não possui vínculos ativos com nenhuma clínica.');
        }

        if ($clinics->count() === 1) {
            session(['active_clinic_id' => $clinics->first()->id]);
            return redirect()->route('dashboard');
        }

        return view('auth.select-clinic', compact('clinics'));
    }

    /**
     * Processa a seleção da clínica ativa.
     */
    public function select(Request $request): RedirectResponse
    {
        $request->validate([
            'clinic_id' => 'required|integer'
        ]);

        $user = auth()->user();
        $clinicId = $request->clinic_id;
        
        $clinic = \App\Models\Clinic::find($clinicId);

        if (!$clinic || (!$user->is_admin && $clinic->academy_company_id !== $user->academy_company_id)) {
            return redirect()->back()->with('error', 'Acesso negado à unidade selecionada.');
        }

        session(['active_clinic_id' => $clinicId]);
        $user->update(['clinic_id' => $clinicId]); // Opcional: salva como preferência

        session()->save(); // Garante persistência imediata

        \Log::debug('ClinicSelection@select | Session set for clinic: ' . session('active_clinic_id'));

        // Redireciona para o portal do paciente se o usuário tiver esse papel
        if ($user->hasRole('paciente')) {
             \Log::debug('ClinicSelection@select | Redirecting Patient to portal');
             return redirect()->route('patient.portal');
        }

        \Log::debug('ClinicSelection@select | Redirecting to dashboard');
        return redirect()->route('dashboard');
    }
}
