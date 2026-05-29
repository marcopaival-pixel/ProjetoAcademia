<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class StudentCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $uid = (int) $user->id;

        // Pegar mês e ano da query ou usar atual
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        
        $currentDate = Carbon::createFromDate($year, $month, 1);
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        // Buscar dias com treinos
        $exercises = DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->whereMonth('entry_date', $month)
            ->whereYear('entry_date', $year)
            ->get();

        $entries = $exercises->groupBy('entry_date')->map(function ($dayExercises) {
            return (object) ['count' => $dayExercises->count(), 'items' => $dayExercises];
        });

        // Buscar dias com registros de nutrição (opcional, para um calendário unificado)
        $foods = DB::table('food_entries')
            ->where('user_id', $uid)
            ->whereMonth('entry_date', $month)
            ->whereYear('entry_date', $year)
            ->get();

        $nutritionEntries = $foods->groupBy('entry_date')->map(function ($dayFoods) {
            return (object) ['count' => $dayFoods->count(), 'items' => $dayFoods];
        });

        return view('student.calendar', [
            'month' => $month,
            'year' => $year,
            'currentDate' => $currentDate,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'entries' => $entries,
            'nutritionEntries' => $nutritionEntries,
            'isPremium' => $user->hasPremiumAccess(),
        ]);
    }
}
