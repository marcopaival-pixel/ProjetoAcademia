<?php

namespace App\Http\Controllers\Representative;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $clinics = \App\Models\Clinic::where('representative_id', $user->id)->get();
        $commissions = \App\Models\Commission::where('representative_id', $user->id)->get();
        
        $totalSales = $clinics->count();
        $paidCommissions = $commissions->where('status', \App\Models\Commission::STATUS_PAGO)->sum('commission_amount');
        $pendingCommissions = $commissions->whereIn('status', [\App\Models\Commission::STATUS_PENDENTE, \App\Models\Commission::STATUS_DISPONIVEL])->sum('commission_amount');
        
        if ($request->has('export')) {
            $type = $request->get('export');
            if ($type === 'csv') {
                return $this->exportCsv($clinics, $commissions);
            }
            // Para PDF, podemos usar a mesma view formatada para impressão
            if ($type === 'pdf') {
                return view('representative.reports.pdf', compact('clinics', 'commissions', 'totalSales', 'paidCommissions', 'pendingCommissions'));
            }
        }

        return view('representative.reports.index', compact(
            'clinics', 'commissions', 'totalSales', 'paidCommissions', 'pendingCommissions'
        ));
    }

    private function exportCsv($clinics, $commissions)
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=relatorio_vendas.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($clinics, $commissions) {
            $file = fopen('php://output', 'w');
            
            // Clínicas
            fputcsv($file, ['CLINICAS VENDIDAS']);
            fputcsv($file, ['Nome', 'Plano', 'Data Venda', 'Status', 'Valor', 'Comissao']);
            foreach ($clinics as $c) {
                fputcsv($file, [$c->name, $c->plan_name, $c->sale_date ? $c->sale_date->format('d/m/Y') : '', $c->sale_status, $c->commission_value, $c->commission_type]);
            }
            
            fputcsv($file, []);
            
            // Comissões
            fputcsv($file, ['COMISSOES']);
            fputcsv($file, ['Data', 'Status', 'Valor Base', 'Comissao']);
            foreach ($commissions as $com) {
                fputcsv($file, [$com->created_at->format('d/m/Y'), $com->status, $com->base_amount, $com->commission_amount]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
