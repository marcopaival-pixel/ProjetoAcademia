<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ShopVendor;
use App\Services\Shop\ShopSalesReportService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShopAdminReportController extends Controller
{
    public function __construct(private ShopSalesReportService $reportService) {}

    public function index(Request $request): View
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->get('from'))->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->get('to'))->endOfDay()
            : now()->endOfDay();

        $report = $this->reportService->summary($from, $to);
        $vendors = ShopVendor::orderBy('name')->get(['id', 'name']);

        return view('admin.shop.reports.index', compact('report', 'from', 'to', 'vendors'));
    }

    public function payVendorCommissions(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => ['required', 'integer', 'exists:shop_vendors,id'],
            'until' => ['nullable', 'date'],
        ]);

        $until = isset($validated['until'])
            ? Carbon::parse($validated['until'])->endOfDay()
            : now()->endOfDay();

        $updated = $this->reportService->markVendorCommissionsPaid(
            (int) $validated['vendor_id'],
            $until
        );

        $vendor = ShopVendor::findOrFail($validated['vendor_id']);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Marcou {$updated} comissão(ões) shopping como pagas — vendor #{$vendor->id}",
            'ip_address' => $request->ip(),
            'payload' => ['vendor_id' => $vendor->id, 'updated' => $updated, 'until' => $until->toDateString()],
        ]);

        return redirect()
            ->route('admin.shop.reports.index', $request->only(['from', 'to']))
            ->with('success', "Foram liquidadas {$updated} linha(s) de comissão para {$vendor->name}.");
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->get('from'))->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->get('to'))->endOfDay()
            : now()->endOfDay();

        $type = $request->get('type', 'summary');
        $report = $this->reportService->summary($from, $to);
        $filename = 'shop_'.$type.'_'.$from->format('Y-m-d').'_'.$to->format('Y-m-d').'.csv';

        if ($type === 'orders') {
            $orders = $this->reportService->ordersForExport($from, $to);

            return response()->streamDownload(function () use ($orders) {
                $out = fopen('php://output', 'w');
                fputcsv($out, [
                    'Pedido', 'Aluno', 'E-mail', 'Total', 'Desconto', 'Status', 'Pago em', 'Gateway',
                ], ';');
                foreach ($orders as $order) {
                    fputcsv($out, [
                        $order->order_number,
                        $order->user?->name ?? '',
                        $order->user?->email ?? '',
                        number_format((float) $order->total, 2, '.', ''),
                        number_format((float) $order->discount_amount, 2, '.', ''),
                        $order->status,
                        $order->paid_at?->format('Y-m-d H:i:s') ?? '',
                        $order->payment_gateway ?? '',
                    ], ';');
                }
                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Métrica', 'Valor'], ';');
            fputcsv($out, ['Pedidos pagos', $report['order_count']], ';');
            fputcsv($out, ['Receita bruta', number_format($report['gross_revenue'], 2, '.', '')], ';');
            fputcsv($out, ['Descontos', number_format($report['discount_total'], 2, '.', '')], ';');
            fputcsv($out, ['Comissões pendentes', number_format($report['pending_commissions'], 2, '.', '')], ';');
            fputcsv($out, ['Comissões pagas', number_format($report['paid_commissions'], 2, '.', '')], ';');
            fputcsv($out, [], ';');
            fputcsv($out, ['Parceiro', 'Total comissão', 'Pendente', 'Paga', 'Itens'], ';');
            foreach ($report['by_vendor'] as $row) {
                fputcsv($out, [
                    $row['vendor_name'],
                    number_format($row['commission_total'], 2, '.', ''),
                    number_format($row['commission_pending'], 2, '.', ''),
                    number_format($row['commission_paid'], 2, '.', ''),
                    $row['item_count'],
                ], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
