<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard profissional (Para Nutrólogos, Nutricionistas e Personals).
     */
    public function index(Request $request): View
    {
        // Simulando dados de uma clínica/tenant para demonstração da interface moderna
        $stats = [
            'active_patients' => 128,
            'active_plans' => 45,
            'new_assessments' => 12,
            'retention_rate' => 94,
            'revenue_month' => 'R$ 15.400,00',
        ];

        // Simulando lista de "Ações Próximas"
        $tasks = [
            ['id' => 1, 'type' => 'alert', 'msg' => 'Carlos Silva atingiu platô de peso (14 dias)', 'priority' => 'high'],
            ['id' => 2, 'type' => 'plan', 'msg' => 'Prescrever nova dieta para Maria Oliveira', 'priority' => 'medium'],
            ['id' => 3, 'type' => 'assessment', 'msg' => 'Avaliação física pendente: João Santos', 'priority' => 'low'],
        ];

        // Simulando dados de engajamento dos pacientes nos últimos 7 dias (para o gráfico)
        $engagementData = [85, 88, 76, 92, 95, 89, 91];

        return view('professional.dashboard', compact('stats', 'tasks', 'engagementData'));
    }
}
