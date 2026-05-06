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
        
        $clinics = DB::table('clinic_user')
            ->join('academy_companies', 'clinic_user.academy_company_id', '=', 'academy_companies.id')
            ->where('clinic_user.user_id', $user->id)
            ->where('clinic_user.status', 'active')
            ->select('academy_companies.*', 'clinic_user.role')
            ->get();

        if ($clinics->isEmpty()) {
             // Fallback para comportamento legado
             if ($user->academy_company_id) {
                 session(['active_clinic_id' => $user->academy_company_id]);
                 return redirect()->route('dashboard');
             }
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
        
        $hasAccess = DB::table('clinic_user')
            ->where('user_id', $user->id)
            ->where('academy_company_id', $clinicId)
            ->where('status', 'active')
            ->exists();

        \Log::debug('ClinicSelection@select | User: ' . $user->email . ' | Clinic: ' . $clinicId . ' | HasAccess: ' . ($hasAccess ? 'YES' : 'NO'));

        // Admin global sempre tem acesso (para suporte/gestão)
        if (!$hasAccess && !$user->is_admin && $user->academy_company_id != $clinicId) {
            return redirect()->back()->with('error', 'Acesso negado à unidade selecionada.');
        }

        session(['active_clinic_id' => $clinicId]);
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
