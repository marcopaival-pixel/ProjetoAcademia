<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalInvoice;
use App\Services\FiscalInvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FiscalInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('admin.financial.management');

        $query = FiscalInvoice::with(['payment.user', 'logs'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('provider_invoice_id', 'like', "%{$search}%")
                    ->orWhereHas('payment', fn ($payment) => $payment->where('gateway_id', 'like', "%{$search}%"))
                    ->orWhereHas('payment.user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        return view('admin.fiscal-invoices.index', [
            'invoices' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function retry(FiscalInvoice $fiscalInvoice, FiscalInvoiceService $service): RedirectResponse
    {
        $this->authorize('admin.financial.management');

        $service->reprocess($fiscalInvoice, auth()->id());

        return back()->with('success', 'Reprocessamento fiscal solicitado.');
    }

    public function cancel(Request $request, FiscalInvoice $fiscalInvoice, FiscalInvoiceService $service): RedirectResponse
    {
        $this->authorize('admin.financial.management');

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $service->cancel($fiscalInvoice, $data['reason'], auth()->id());

        return back()->with('success', 'Cancelamento fiscal processado.');
    }
}
