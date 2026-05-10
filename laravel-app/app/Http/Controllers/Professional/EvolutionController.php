<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HealthAlert;
use App\Models\BodyAssessment;
use Illuminate\Support\Facades\DB;

class EvolutionController extends Controller
{
    public function index(Request $request)
    {
        $professional = auth()->user();
        $patients = $professional->patients()->with(['profile'])->get();

        // 1. Média de Saúde da Base
        $avgHealthScore = $patients->avg('health_score') ?? 0;

        // 2. Alunos em Risco (Health Score < 40 ou Alertas Danger)
        $riskPatients = $patients->filter(function($u) {
            return ($u->health_score !== null && $u->health_score < 40) || 
                   HealthAlert::where('user_id', $u->id)->where('severity', 'danger')->where('is_read', false)->exists();
        });

        // 3. Distribuição de Metas
        $goalDistribution = $patients->groupBy(function($u) {
            return $u->profile?->goal ?? 'Não definido';
        })->map(fn($group) => $group->count());

        // 4. Últimas Avaliações Inteligentes
        $recentAssessments = BodyAssessment::whereIn('user_id', $patients->pluck('id'))
            ->with('user')
            ->orderByDesc('assessment_date')
            ->limit(10)
            ->get();

        return view('professional.evolution.index', compact(
            'patients',
            'avgHealthScore',
            'riskPatients',
            'goalDistribution',
            'recentAssessments'
        ));
    }
}
