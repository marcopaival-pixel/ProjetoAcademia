<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class MonthlyReportAggregator
{
    /**
     * @return array{
     *     days: array<string, array{label: string, kcal_in: int, ex_min: int, ex_kcal: int, weight: float|null}>,
     *     avg_kcal: int,
     *     days_with_food: int,
     *     total_ex_min: int,
     *     total_ex_kcal: int,
     *     delta_weight: float|null,
     *     first_weight: float|null,
     *     last_weight: float|null,
     * }
     */
    public static function forUserMonth(int $userId, CarbonInterface $monthStart, CarbonInterface $monthEnd): array
    {
        $d0 = $monthStart->format('Y-m-d');
        $d1 = $monthEnd->format('Y-m-d');

        $from = Carbon::parse($d0)->startOfDay();
        $to = Carbon::parse($d1)->startOfDay();

        $days = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $days[$key] = [
                'label' => $d->translatedFormat('d/m D'),
                'kcal_in' => 0,
                'ex_min' => 0,
                'ex_kcal' => 0,
                'weight' => null,
            ];
        }

        foreach (DB::table('food_entries')
            ->where('user_id', $userId)
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
            ->where('user_id', $userId)
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
            ->where('user_id', $userId)
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

        $weightsInPeriod = DB::table('weight_entries')
            ->where('user_id', $userId)
            ->whereBetween('weighed_at', [$d0, $d1])
            ->orderBy('weighed_at')
            ->get();

        $deltaWeight = null;
        $firstWeight = null;
        $lastWeight = null;
        if ($weightsInPeriod->count() >= 2) {
            $firstWeight = (float) $weightsInPeriod->first()->weight_kg;
            $lastWeight = (float) $weightsInPeriod->last()->weight_kg;
            $deltaWeight = round($lastWeight - $firstWeight, 2);
        } elseif ($weightsInPeriod->count() === 1) {
            $lastBefore = DB::table('weight_entries')
                ->where('user_id', $userId)
                ->where('weighed_at', '<', $d0)
                ->orderByDesc('weighed_at')
                ->first();
            if ($lastBefore) {
                $firstWeight = (float) $lastBefore->weight_kg;
                $lastWeight = (float) $weightsInPeriod->first()->weight_kg;
                $deltaWeight = round($lastWeight - $firstWeight, 2);
            } else {
                $lastWeight = (float) $weightsInPeriod->first()->weight_kg;
            }
        }

        return [
            'days' => $days,
            'avg_kcal' => $avgKcal,
            'days_with_food' => $daysWithFood,
            'total_ex_min' => $totalExMin,
            'total_ex_kcal' => $totalExKcal,
            'delta_weight' => $deltaWeight,
            'first_weight' => $firstWeight,
            'last_weight' => $lastWeight,
        ];
    }
}
