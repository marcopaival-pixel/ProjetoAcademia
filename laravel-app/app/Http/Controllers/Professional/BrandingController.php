<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandingController extends Controller
{
    /**
     * Exibe o estúdio de personalização (White Label).
     */
    public function index(): View
    {
        // Simulando as configurações atuais do profissional
        $branding = [
            'clinic_name' => 'Clínica NexShape Pro',
            'primary_color' => '#3b82f6',
            'accent_color' => '#10b981',
            'logo_url' => null,
            'custom_domain' => 'clinica.nexshape.app',
        ];

        return view('professional.branding.index', compact('branding'));
    }

    /**
     * Atualiza as configurações de marca.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'clinic_name' => 'required|string|max:100',
            'primary_color' => 'required|string|size:7',
            'accent_color' => 'required|string|size:7',
            'logo' => 'nullable|image|max:2048',
        ]);

        // Lógica de salvamento (Simulação SaaS)
        // Em um sistema real: ProfessionalBranding::updateOrCreate(['user_id' => auth()->id()], $data);

        return back()->with('success', 'Identidade visual atualizada com sucesso! Suas exportações e portal do paciente agora refletem sua marca.');
    }
}
