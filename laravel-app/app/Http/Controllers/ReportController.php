<?php

namespace App\Http\Controllers;

use App\Services\MonthlyReportAggregator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $range = (int) $request->get('range', 7);
        $end = Carbon::today();
        $start = $end->copy()->subDays($range - 1);

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
        ]);
    }
}
