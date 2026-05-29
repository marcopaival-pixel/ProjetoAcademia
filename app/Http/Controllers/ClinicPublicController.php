<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicPublicController extends Controller
{
    /**
     * Exibe a página pública da clínica.
     */
    public function show(string $slug): View
    {
        $clinic = Clinic::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Setar o contexto da clínica para esta requisição
        TenantContext::set($clinic->id);
        
        // Armazenar na sessão para navegação subsequente
        session(['active_clinic_id' => $clinic->id]);

        return view('clinic.public_home', compact('clinic'));
    }
}
