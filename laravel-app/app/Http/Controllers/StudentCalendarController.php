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
        $entries = DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->whereMonth('entry_date', $month)
            ->whereYear('entry_date', $year)
            ->select('entry_date', DB::raw('count(*) as count'))
            ->groupBy('entry_date')
            ->get()
            ->keyBy('entry_date');

        // Buscar dias com registros de nutrição (opcional, para um calendário unificado)
        $nutritionEntries = DB::table('food_entries')
            ->where('user_id', $uid)
            ->whereMonth('entry_date', $month)
            ->whereYear('entry_date', $year)
            ->select('entry_date', DB::raw('count(*) as count'))
            ->groupBy('entry_date')
            ->get()
            ->keyBy('entry_date');

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
