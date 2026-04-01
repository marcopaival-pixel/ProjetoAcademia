<?php

namespace App\Http\Controllers;

use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $uid = (int) $request->user()->id;
        $end = \Illuminate\Support\Carbon::today();
        $start = $end->copy()->subDays(6);

        $d0 = $start->format('Y-m-d');
        $d1 = $end->format('Y-m-d');

        $days = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $key = $d->format('Y-m-d');
            $days[$key] = [
                'label' => $d->format('d/m D'),
                'kcal_in' => 0,
                'ex_min' => 0,
                'ex_kcal' => 0,
                'weight' => null,
            ];
        }

        foreach (DB::table('food_entries')
            ->where('user_id', $uid)
            ->whereBetween('entry_date', [$d0, $d1])
            ->selectRaw('entry_date, COALESCE(SUM(calories), 0) AS c')
            ->groupBy('entry_date')
            ->get() as $r) {
            $k = $r->entry_date;
            if (isset($days[$k])) {
                $days[$k]['kcal_in'] = (int) $r->c;
            }
        }

        foreach (DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->whereBetween('entry_date', [$d0, $d1])
            ->selectRaw('entry_date, COALESCE(SUM(duration_min), 0) AS dm, COALESCE(SUM(calories_burned), 0) AS bk')
            ->groupBy('entry_date')
            ->get() as $r) {
            $k = $r->entry_date;
            if (isset($days[$k])) {
                $days[$k]['ex_min'] = (int) $r->dm;
                $days[$k]['ex_kcal'] = (int) $r->bk;
            }
        }

        foreach (DB::table('weight_entries')
            ->where('user_id', $uid)
            ->whereBetween('weighed_at', [$d0, $d1])
            ->orderBy('weighed_at')
            ->get() as $r) {
            $k = $r->weighed_at;
            if (isset($days[$k])) {
                $days[$k]['weight'] = (float) $r->weight_kg;
            }
        }

        $totalKcal = 0;
        $daysWithFood = 0;
        $totalExMin = 0;
        $totalExKcal = 0;
        foreach ($days as $info) {
            if ($info['kcal_in'] > 0) {
                $totalKcal += $info['kcal_in'];
                $daysWithFood++;
            }
            $totalExMin += $info['ex_min'];
            $totalExKcal += $info['ex_kcal'];
        }

        $avgKcal = $daysWithFood > 0 ? (int) round($totalKcal / $daysWithFood) : 0;

        $lastBefore = DB::table('weight_entries')
            ->where('user_id', $uid)
            ->where('weighed_at', '<=', $d1)
            ->orderByDesc('weighed_at')
            ->first();

        $weightsInPeriod = DB::table('weight_entries')
            ->where('user_id', $uid)
            ->whereBetween('weighed_at', [$d0, $d1])
            ->orderBy('weighed_at')
            ->get();

        $deltaWeight = null;
        if ($weightsInPeriod->count() >= 2) {
            $first = (float) $weightsInPeriod->first()->weight_kg;
            $last = (float) $weightsInPeriod->last()->weight_kg;
            $deltaWeight = round($last - $first, 2);
        } elseif ($weightsInPeriod->count() === 1 && $lastBefore && $lastBefore->weighed_at < $d0) {
            $deltaWeight = round((float) $weightsInPeriod->first()->weight_kg - (float) $lastBefore->weight_kg, 2);
        }

        return view('report', [
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'avgKcal' => $avgKcal,
            'daysWithFood' => $daysWithFood,
            'totalExMin' => $totalExMin,
            'totalExKcal' => $totalExKcal,
            'deltaWeight' => $deltaWeight,
        ]);
    }
}
