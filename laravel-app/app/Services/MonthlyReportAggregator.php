<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class MonthlyReportAggregator
{
    /**
     * @return array{
     *     days: array<string, array{
     *         label: string, 
     *         kcal_in: int, 
     *         p: float, 
     *         c: float, 
     *         f: float, 
     *         ex_min: int, 
     *         ex_kcal: int, 
     *         water: int, 
     *         weight: float|null
     *     }>,
     *     avg_kcal: int,
     *     avg_p: float,
     *     avg_c: float,
     *     avg_f: float,
     *     days_with_food: int,
     *     total_ex_min: int,
     *     total_ex_kcal: int,
     *     total_water: int,
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
                'p' => 0.0,
                'c' => 0.0,
                'f' => 0.0,
                'ex_min' => 0,
                'ex_kcal' => 0,
                'water' => 0,
                'weight' => null,
            ];
        }

        // Food & Macros
        foreach (DB::table('food_entries')
            ->where('user_id', $userId)
            ->whereBetween('entry_date', [$d0, $d1])
            ->selectRaw('entry_date, SUM(calories) as cal, SUM(protein_g) as p, SUM(carbs_g) as c, SUM(fat_g) as f')
            ->groupBy('entry_date')
            ->get() as $r) {
            $k = $r->entry_date;
            if (isset($days[$k])) {
                $days[$k]['kcal_in'] = (int) $r->cal;
                $days[$k]['p'] = (float) $r->p;
                $days[$k]['c'] = (float) $r->c;
                $days[$k]['f'] = (float) $r->f;
            }
        }

        // Exercises
        foreach (DB::table('exercise_entries')
            ->where('user_id', $userId)
            ->whereBetween('entry_date', [$d0, $d1])
            ->selectRaw('entry_date, SUM(duration_min) as dm, SUM(calories_burned) as bk')
            ->groupBy('entry_date')
            ->get() as $r) {
            $k = $r->entry_date;
            if (isset($days[$k])) {
                $days[$k]['ex_min'] = (int) $r->dm;
                $days[$k]['ex_kcal'] = (int) $r->bk;
            }
        }

        // Water
        foreach (DB::table('water_entries')
            ->where('user_id', $userId)
            ->whereBetween('entry_date', [$d0, $d1])
            ->selectRaw('entry_date, SUM(amount_ml) as ml')
            ->groupBy('entry_date')
            ->get() as $r) {
            $k = $r->entry_date;
            if (isset($days[$k])) {
                $days[$k]['water'] = (int) $r->ml;
            }
        }

        // Weight
        $weightsInPeriod = DB::table('weight_entries')
            ->where('user_id', $userId)
            ->whereBetween('weighed_at', [$d0, $d1])
            ->orderBy('weighed_at')
            ->get();

        foreach ($weightsInPeriod as $r) {
            $k = $r->weighed_at;
            if (isset($days[$k])) {
                $days[$k]['weight'] = (float) $r->weight_kg;
            }
        }

        // Totals & Averages
        $totalKcal = 0; $totalP = 0.0; $totalC = 0.0; $totalF = 0.0;
        $daysWithFood = 0;
        $daysWithEx = 0;
        $totalExMin = 0; $totalExKcal = 0;
        $totalWater = 0;

        foreach ($days as $info) {
            if ($info['kcal_in'] > 0) {
                $totalKcal += $info['kcal_in'];
                $totalP += $info['p'];
                $totalC += $info['c'];
                $totalF += $info['f'];
                $daysWithFood++;
            }
            if ($info['ex_min'] > 0) {
                $daysWithEx++;
            }
            $totalExMin += $info['ex_min'];
            $totalExKcal += $info['ex_kcal'];
            $totalWater += $info['water'];
        }

        $avgKcal = $daysWithFood > 0 ? (int) round($totalKcal / $daysWithFood) : 0;
        $avgP = $daysWithFood > 0 ? $totalP / $daysWithFood : 0.0;
        $avgC = $daysWithFood > 0 ? $totalC / $daysWithFood : 0.0;
        $avgF = $daysWithFood > 0 ? $totalF / $daysWithFood : 0.0;

        // Weight Delta
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
            'avg_p' => $avgP,
            'avg_c' => $avgC,
            'avg_f' => $avgF,
            'days_with_food' => $daysWithFood,
            'days_with_ex' => $daysWithEx,
            'total_ex_min' => $totalExMin,
            'total_ex_kcal' => $totalExKcal,
            'total_water' => $totalWater,
            'delta_weight' => $deltaWeight,
            'first_weight' => $firstWeight,
            'last_weight' => $lastWeight,
        ];
    }
}
