<?php

namespace App\Http\Controllers\Professional\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->professionalProfile || !$user->professionalProfile->use_finance_module) {
            return redirect()->route('professional.dashboard')->with('error', 'Módulo financeiro não ativado.');
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $revenueMonth = \App\Models\ProfessionalFinanceEntry::where('professional_id', $user->id)
            ->where('type', 'revenue')
            ->whereMonth('due_date', $currentMonth)
            ->whereYear('due_date', $currentYear)
            ->sum('amount');

        $expenseMonth = \App\Models\ProfessionalFinanceEntry::where('professional_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('due_date', $currentMonth)
            ->whereYear('due_date', $currentYear)
            ->sum('amount');

        $netProfit = $revenueMonth - $expenseMonth;

        $unpaidEntriesCount = \App\Models\ProfessionalFinanceEntry::where('professional_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $overdueEntriesCount = \App\Models\ProfessionalFinanceEntry::where('professional_id', $user->id)
            ->where('status', 'pending')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        return view('professional.finance.dashboard', compact(
            'revenueMonth', 
            'expenseMonth', 
            'netProfit', 
            'unpaidEntriesCount', 
            'overdueEntriesCount'
        ));
    }
}
