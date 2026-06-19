<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Especialidade;

class SpecialtyContextController extends Controller
{
    /**
     * Alterna a especialidade ativa do profissional na sessão.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'especialidade_id' => 'required|exists:especialidades,id'
        ]);

        $user = auth()->user();

        if (!$user || !$user->hasRole('professional')) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $profile = $user->professionalProfile;
        
        if (!$profile) {
            return response()->json(['error' => 'Perfil profissional não encontrado'], 404);
        }

        // Verifica se o profissional tem essa especialidade
        $hasSpecialty = $profile->especialidade_id == $request->especialidade_id || 
                        $profile->especialidades()->where('especialidade_id', $request->especialidade_id)->exists();

        if (!$hasSpecialty) {
            return response()->json(['error' => 'Especialidade não vinculada ao seu perfil'], 403);
        }

        session(['active_specialty_id' => $request->especialidade_id]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Contexto de especialidade atualizado.');
    }
}
