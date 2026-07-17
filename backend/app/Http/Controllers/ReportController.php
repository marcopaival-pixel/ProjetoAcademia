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

        return view('report', [
            'start' => $start,
            'end' => $end,
            'range' => $range,
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
            'physical' => $data['physical'],
            'goals' => $data['goals'],
            'adherence' => $data['adherence'],
            'isPremium' => $this->monetizationService->hasPremium($request->user()),
        ]);
    }
}
