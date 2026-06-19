<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalPatient;
use Illuminate\Http\Request;

class ProfessionalManagementController extends Controller
{
    public function index()
    {
        $patient = auth()->user();
        
        $links = ProfessionalPatient::with(['professional.professionalProfile.profession'])
            ->where('user_id', $patient->id)
            ->where('status', 'Sim')
            ->get();

        return view('patient.my-professionals', compact('links'));
    }

    public function updatePermissions(Request $request, ProfessionalPatient $link)
    {
        // Ensure the link belongs to the auth user
        if ($link->user_id !== auth()->id()) {
            abort(403);
        }

        $permissions = $request->input('permissions', []);
        
        // Assegurar formato correto de patient_permissions no DB
        $link->update([
            'patient_permissions' => $permissions
        ]);

        return back()->with('success', 'Permissões atualizadas com sucesso!');
    }

    public function revoke(ProfessionalPatient $link)
    {
        // Ensure the link belongs to the auth user
        if ($link->user_id !== auth()->id()) {
            abort(403);
        }

        // Marcar como revogado em vez de deletar para manter o histórico
        $link->update([
            'status' => 'Revogado'
        ]);

        return back()->with('success', 'Vínculo revogado com sucesso. O profissional não tem mais acesso aos seus dados.');
    }
}
