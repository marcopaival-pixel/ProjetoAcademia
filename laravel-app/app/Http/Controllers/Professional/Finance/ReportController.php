<?php

namespace App\Http\Controllers\Professional\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\ProfessionalFinanceEntry::class);

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $professionalId = auth()->id();

        // 1. Visão Geral do Mês Selecionado (apenas status 'paid' para relatórios exatos, ou todos se preferir. Vamos usar 'paid' para receitas e despesas efetivadas)
        $monthlyRevenue = \App\Models\ProfessionalFinanceEntry::where('professional_id', $professionalId)
            ->where('type', 'revenue')
            ->where('status', 'paid')
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->sum('amount');

        $monthlyExpense = \App\Models\ProfessionalFinanceEntry::where('professional_id', $professionalId)
            ->where('type', 'expense')
            ->where('status', 'paid')
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->sum('amount');

        $netProfit = $monthlyRevenue - $monthlyExpense;

        // 2. Análise por Categoria (Despesas) do mês selecionado
        $expensesByCategory = \App\Models\ProfessionalFinanceEntry::with('category')
            ->where('professional_id', $professionalId)
            ->where('type', 'expense')
            ->where('status', 'paid')
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($monthlyExpense) {
                $categoryName = $item->category ? $item->category->name : 'Sem Categoria';
                $percentage = $monthlyExpense > 0 ? ($item->total / $monthlyExpense) * 100 : 0;
                return [
                    'name' => $categoryName,
                    'total' => $item->total,
                    'percentage' => round($percentage, 1)
                ];
            });

        // 3. Histórico dos últimos 6 meses (Fluxo de Caixa)
        $historicalData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $m = $date->month;
            $y = $date->year;

            $rev = \App\Models\ProfessionalFinanceEntry::where('professional_id', $professionalId)
                ->where('type', 'revenue')
                ->where('status', 'paid')
                ->whereYear('payment_date', $y)
                ->whereMonth('payment_date', $m)
                ->sum('amount');

            $exp = \App\Models\ProfessionalFinanceEntry::where('professional_id', $professionalId)
                ->where('type', 'expense')
                ->where('status', 'paid')
                ->whereYear('payment_date', $y)
                ->whereMonth('payment_date', $m)
                ->sum('amount');

            $historicalData->push([
                'month_name' => strftime('%b/%Y', mktime(0, 0, 0, $m, 1, $y)),
                'revenue' => $rev,
                'expense' => $exp,
                'profit' => $rev - $exp
            ]);
        }

        return view('professional.finance.reports.index', compact(
            'year',
            'month',
            'monthlyRevenue',
            'monthlyExpense',
            'netProfit',
            'expensesByCategory',
            'historicalData'
        ));
    }
}
