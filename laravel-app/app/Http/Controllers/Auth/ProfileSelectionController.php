<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileSelectionController extends Controller
{
    /**
     * Exibe a tela de seleção de perfil.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = auth()->user();
        
        // Se já tem escolha salva, redireciona direto
        if ($user->remember_profile && $user->hasRole($user->remember_profile)) {
            return $this->redirectBasedOnRole($user->remember_profile);
        }

        $roles = $user->roles;

        if ($roles->count() <= 1) {
            return $this->redirectBasedOnRole($roles->first()?->name ?? 'aluno');
        }

        return view('auth.select-profile', compact('roles'));
    }

    /**
     * Processa a seleção do perfil.
     */
    public function select(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => 'required|string', // Permite 'all'
            'remember' => 'nullable|boolean'
        ]);

        $roleName = $request->role;
        $user = auth()->user();

        if ($roleName !== 'all' && !$user->hasRole($roleName)) {
            return redirect()->back()->with('error', 'Você não possui este perfil.');
        }

        if ($request->remember && $roleName !== 'all') {
            $user->update(['remember_profile' => $roleName]);
        }

        session(['active_role' => $roleName]);

        return $this->redirectBasedOnRole($roleName);
    }

    /**
     * Redireciona o usuário com base no papel selecionado.
     */
    private function redirectBasedOnRole(string $roleName): RedirectResponse
    {
        session(['active_role' => $roleName]);

        if ($roleName === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($roleName === 'professional') {
            return redirect()->route('professional.dashboard');
        }

        if ($roleName === 'paciente') {
            return redirect()->route('patient.portal');
        }

        if ($roleName === 'all') {
            // Se escolher todos, redireciona para um painel unificado ou o principal
            return redirect()->route('dashboard');
        }

        return redirect()->route('dashboard');
    }
}
