<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Lista todos os pacientes vinculados ao profissional.
     */
    public function index(Request $request): View
    {
        // Simulando a busca de pacientes vinculados
        // Em um sistema real, usaríamos a relação: auth()->user()->patients
        $patients = [
            [
                'id' => 1,
                'name' => 'Carlos Silva',
                'email' => 'carlos@exemplo.com',
                'goal' => 'Hipertrofia',
                'status' => 'active',
                'last_activity' => 'Hoje, 10:45',
                'bf_evolution' => -2.5,
                'weight_evolution' => +3.2,
                'avatar' => 'CS'
            ],
            [
                'id' => 2,
                'name' => 'Maria Oliveira',
                'email' => 'maria@exemplo.com',
                'goal' => 'Emagrecimento',
                'status' => 'warning',
                'last_activity' => 'Ontem, 20:10',
                'bf_evolution' => -1.2,
                'weight_evolution' => -4.5,
                'avatar' => 'MO'
            ],
            [
                'id' => 3,
                'name' => 'Ricardo Santos',
                'email' => 'ricardo@exemplo.com',
                'goal' => 'Performance',
                'status' => 'active',
                'last_activity' => 'Há 2 dias',
                'bf_evolution' => -0.5,
                'weight_evolution' => +1.0,
                'avatar' => 'RS'
            ],
        ];

        return view('professional.patients.index', compact('patients'));
    }

    /**
     * Exibe o prontuário eletrônico (EHR) detalhado do paciente.
     */
    public function show($id): View
    {
        // Simulando dados detalhados do paciente
        $patient = [
            'id' => $id,
            'name' => $id == 1 ? 'Carlos Silva' : 'Paciente',
            'age' => 28,
            'height' => 182,
            'weight' => 84.5,
            'formula' => 'Cunningham',
            'activity_level' => 'Moderado',
            'goal' => 'Hipertrofia Máxima',
            'biotype' => 'Mesomorfo',
        ];

        // Dados de evolução para o gráfico
        $evolutionData = [86.0, 85.5, 85.2, 84.8, 84.5];
        $dates = ['Jan', 'Fev', 'Mar', 'Abr', 'Maio'];

        return view('professional.patients.show', compact('patient', 'evolutionData', 'dates'));
    }
}
