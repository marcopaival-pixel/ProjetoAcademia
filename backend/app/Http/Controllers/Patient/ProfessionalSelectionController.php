<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProfessionalSelectionController extends Controller
{
    /**
     * Exibe a tela de seleção de profissional.
     */
    public function index()
    {
        $patient = auth()->user();
        $professionals = $patient->professionals()->wherePivot('status', 'Sim')->get();

        if ($professionals->count() === 1) {
            $professional = $professionals->first();
            $this->setProfessionalSession($professional->id);
            return redirect()->route('patient.portal');
        }

        return view('patient.professional_selection', compact('professionals'));
    }

    /**
     * Define o profissional ativo na sessão.
     */
    public function select(Request $request)
    {
        $request->validate([
            'professional_id' => 'required|exists:users,id'
        ]);

        $patient = auth()->user();
        
        // Verifica se o profissional realmente tem vínculo ativo com o paciente
        $hasLink = $patient->professionals()
            ->where('profissional_id', $request->professional_id)
            ->wherePivot('status', 'Sim')
            ->exists();

        if (!$hasLink) {
            return back()->with('error', 'Vínculo profissional inválido ou inativo.');
        }

        $this->setProfessionalSession($request->professional_id);

        return redirect()->route('patient.portal')
            ->with('success', 'Profissional selecionado com sucesso.');
    }

    private function setProfessionalSession($id)
    {
        Session::put('active_professional_id', $id);
    }
}
