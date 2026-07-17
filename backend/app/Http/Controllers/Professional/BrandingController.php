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
        $branding = auth()->user()->branding;

        if (!$branding) {
            $branding = [
                'clinic_name' => 'Clínica ' . explode(' ', auth()->user()->name)[0],
                'primary_color' => '#3b82f6',
                'accent_color' => '#10b981',
                'logo_url' => null,
                'custom_domain' => null,
            ];
        } else {
            $branding = $branding->toArray();
            $branding['logo_url'] = $branding['logo_path'] ? Storage::url($branding['logo_path']) : null;
        }

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

        $branding = \App\Models\ProfessionalBranding::firstOrNew(['user_id' => auth()->id()]);
        
        $branding->clinic_name = $data['clinic_name'];
        $branding->primary_color = $data['primary_color'];
        $branding->accent_color = $data['accent_color'];

        if ($request->hasFile('logo')) {
            // Remover anterior se existir
            if ($branding->logo_path) {
                Storage::delete($branding->logo_path);
            }
            $path = $request->file('logo')->store('branding/logos', 'public');
            $branding->logo_path = $path;
        }

        $branding->save();

        return back()->with('success', 'Identidade visual atualizada com sucesso! Suas exportações e portal do paciente agora refletem sua marca.');
    }
}
