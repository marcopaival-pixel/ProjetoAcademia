<?php

namespace App\Http\Controllers;

use App\Support\WeightChartSvg;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WeightController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $uid = (int) $user->id;

        if ($request->isMethod('post')) {
            $request->validate([
                'weighed_at' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
                'weight_kg' => ['required'],
            ]);
            $day = $request->input('weighed_at');
            $w = (float) str_replace(',', '.', (string) $request->input('weight_kg'));
            if ($w < 20 || $w > 400) {
                return back()->with('error', 'Peso fora do intervalo esperado (20–400 kg).')->withInput();
            }
            $existing = DB::table('weight_entries')
                ->where('user_id', $uid)
                ->where('weighed_at', $day)
                ->first();
            if ($existing) {
                DB::table('weight_entries')
                    ->where('id', $existing->id)
                    ->update(['weight_kg' => $w]);
                $notice = 'Peso do dia atualizado.';
            } else {
                DB::table('weight_entries')->insert([
                    'user_id' => $uid,
                    'weighed_at' => $day,
                    'weight_kg' => $w,
                ]);
                $notice = 'Peso registrado.';
            }

            return back()->with('notice', $notice);
        }

        $rows = DB::table('weight_entries')
            ->where('user_id', $uid)
            ->orderByDesc('weighed_at')
            ->limit(60)
            ->get();

        $chartSeries = $rows->reverse()->values()->map(fn ($r) => [
            'weighed_at' => $r->weighed_at,
            'weight_kg' => $r->weight_kg,
        ])->all();
        $weightChartHtml = WeightChartSvg::render($chartSeries);

        $today = now()->format('Y-m-d');

        return view('weight', [
            'rows' => $rows,
            'weightChartHtml' => $weightChartHtml,
            'today' => $today,
            'notice' => session('notice'),
            'error' => session('error'),
        ]);
    }
}
