<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ActiveRestController extends Controller
{
    private $routines = [
        [
            'id' => 1,
            'title' => 'Mobilidade de Quadril - Flow',
            'duration' => '10 min',
            'intensity' => 'Leve',
            'exercises' => [
                'Postura do Pombo (1 min/lado)',
                '90/90 Hip Switches (10 reps)',
                'Cossack Squat (10 reps)',
                'Spiderman Lunge com Rotação (5 reps/lado)'
            ],
            'benefit' => 'Melhora a profundidade do agachamento e reduz dores lombares.'
        ],
        [
            'id' => 2,
            'title' => 'Saúde do Ombro (Scapular Health)',
            'duration' => '8 min',
            'intensity' => 'Média',
            'exercises' => [
                'Face Pulls com Elástico (15 reps)',
                'Alongamento de Peitoral no Batente (1 min)',
                'Scapular Push-ups (15 reps)',
                'YWT Raises (10 reps/cada)'
            ],
            'benefit' => 'Aumenta a estabilidade para supino e desenvolvimentos.'
        ],
        [
            'id' => 3,
            'title' => 'Deep Stretch - Full Body',
            'duration' => '15 min',
            'intensity' => 'Leve',
            'exercises' => [
                'Child Pose (2 min)',
                'Cat-Cow (15 reps)',
                'Forward Fold (2 min)',
                'Alongamento de Quadriceps em Pé (1 min/lado)'
            ],
            'benefit' => 'Acelera a recuperação e melhora o sono.'
        ]
    ];

    public function index(): View
    {
        $routines = $this->routines;
        return view('active-rest.index', compact('routines'));
    }
}
