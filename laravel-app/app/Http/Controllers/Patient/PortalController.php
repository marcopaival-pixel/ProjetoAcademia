<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    /**
     * Exibe o dashboard do paciente, customizado com a marca do profissional.
     */
    public function index(): View
    {
        // Simulando a busca da marca do profissional vinculado ao paciente
        $branding = [
            'clinic_name' => 'Clínica BioFit Pro',
            'primary_color' => '#3b82f6', // Azul Pro por padrão
            'accent_color' => '#10b981',  // Verde Sucesso
            'logo_url' => null,
        ];

        // Dados do dia para o paciente
        $dailyTasks = [
            ['type' => 'meal', 'time' => '12:30', 'title' => 'Almoço: Peixe e Brócolis', 'done' => false],
            ['type' => 'workout', 'time' => '18:00', 'title' => 'Treino A: Superiores', 'done' => false],
            ['type' => 'water', 'time' => 'Check', 'title' => 'Meta de Hidratação (2.5L)', 'done' => true],
        ];

        return view('patient.dashboard', compact('branding', 'dailyTasks'));
    }
}
