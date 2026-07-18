<?php

namespace App\Http\Controllers;

use App\Services\MonthlyReportAggregator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly \App\Services\ReportMonetizationService $monetizationService
    ) {}

    public function __invoke(Request $request): View
    {
        $range = (int) $request->get('range', 7);
        $end = Carbon::today();
        $start = $end->copy()->subDays($range - 1);

        // Aplica limitação de 30 dias para plano Free
        [$start, $end] = $this->monetizationService->applyDateLimit($request->user(), $start, $end);

        // Registrar log de geração
        $this->monetizationService->logGeneration($request->user(), 'Performance Report', [
            'range' => $range,
            'start' => $start->toDateString(),
            'end' => $end->toDateString()
        ]);

        $data = MonthlyReportAggregator::forUserMonth(
            (int) $request->user()->id,
            $start,
            $end
        );

        $periodDays = count($data['days']);
        $hasFoodData = (int) $data['days_with_food'] > 0;
        $hasTrainingData = (int) $data['days_with_ex'] > 0;
        $hasWeightComparison = $data['delta_weight'] !== null
            && $data['first_weight'] !== null
            && $data['last_weight'] !== null;
        $hasPhysicalData = !empty($data['physical']['bf'])
            || !empty($data['physical']['muscle'])
            || !empty(array_filter((array) ($data['physical']['measures'] ?? []), fn ($value) => $value !== null));
        $hasGoals = trim((string) ($data['goals']['objectives'] ?? '')) !== ''
            && ($data['goals']['objectives'] ?? '') !== 'Nenhuma meta definida.';

        $insights = [];
        if ($hasTrainingData) {
            $insights[] = sprintf(
                'Treinos registrados em %d de %d dias, com %d minutos acumulados.',
                (int) $data['days_with_ex'],
                $periodDays,
                (int) $data['total_ex_min']
            );
        } else {
            $insights[] = 'Não há treinos registrados neste período.';
        }

        if ($hasWeightComparison) {
            $direction = $data['delta_weight'] < 0 ? 'reduziu' : ($data['delta_weight'] > 0 ? 'aumentou' : 'permaneceu estável');
            $insights[] = sprintf(
                'O peso %s %.2f kg, de %.2f kg para %.2f kg.',
                $direction,
                abs((float) $data['delta_weight']),
                (float) $data['first_weight'],
                (float) $data['last_weight']
            );
        } else {
            $insights[] = 'Não há medições suficientes para comparar peso com segurança.';
        }

        if ($hasFoodData) {
            $insights[] = sprintf(
                'A média calórica foi calculada somente sobre %d de %d dias com alimentação registrada.',
                (int) $data['days_with_food'],
                $periodDays
            );
        } else {
            $insights[] = 'Não há registros nutricionais suficientes para avaliar ingestão calórica.';
        }

        return view('report', [
            'start' => $start,
            'end' => $end,
            'range' => $range,
            'periodLabel' => $start->translatedFormat('d/m/Y') . ' a ' . $end->translatedFormat('d/m/Y'),
            'periodDays' => $periodDays,
            'days' => $data['days'],
            'avgKcal' => $data['avg_kcal'],
            'avgP' => $data['avg_p'],
            'avgC' => $data['avg_c'],
            'avgF' => $data['avg_f'],
            'totals' => (object)[
                'kcal' => (int)($data['avg_kcal'] * $data['days_with_food']),
                'p' => (float)($data['avg_p'] * $data['days_with_food']),
                'c' => (float)($data['avg_c'] * $data['days_with_food']),
                'f' => (float)($data['avg_f'] * $data['days_with_food']),
                'ex_min' => (int)$data['total_ex_min'],
                'ex_kcal' => (int)$data['total_ex_kcal'],
                'water' => (int)$data['total_water'],
                'days_food' => (int)$data['days_with_food'],
                'days_ex' => (int)$data['days_with_ex'],
            ],
            'deltaWeight' => $data['delta_weight'],
            'firstWeight' => $data['first_weight'],
            'lastWeight' => $data['last_weight'],
            'physical' => $data['physical'],
            'goals' => $data['goals'],
            'adherence' => $data['adherence'],
            'reportState' => [
                'has_food_data' => $hasFoodData,
                'has_training_data' => $hasTrainingData,
                'has_weight_comparison' => $hasWeightComparison,
                'has_physical_data' => $hasPhysicalData,
                'has_goals' => $hasGoals,
            ],
            'insights' => $insights,
            'isPremium' => $this->monetizationService->hasPremium($request->user()),
        ]);
    }
}
