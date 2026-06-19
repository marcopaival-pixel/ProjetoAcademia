<?php

namespace App\Http\Controllers\Representative;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use Barryvdh\DomPDF\Facade\Pdf;

class SimulatorController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->representativeProfile) {
            return redirect()->route('representative.dashboard')->with('error', 'Perfil de representante incompleto. Contate o administrador.');
        }

        $plans = Plan::where('is_active', true)->where('type', 'clinic')->get();
        $maxDiscount = $user->representativeProfile->max_discount_rate;
        $commissionRate = $user->representativeProfile->commission_rate;

        return view('representative.simulator.index', compact('plans', 'maxDiscount', 'commissionRate'));
    }

    public function generatePdf(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'base_price' => 'required|numeric',
            'discount_rate' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric',
            'final_price' => 'required|numeric'
        ]);

        $user = auth()->user();
        if (!$user->representativeProfile) {
            abort(403, 'Perfil de representante incompleto.');
        }

        $plan = Plan::findOrFail($request->plan_id);
        $validityDate = now()->addDays(7)->format('d/m/Y');

        $data = [
            'representative' => $user,
            'plan' => $plan,
            'basePrice' => $request->base_price,
            'discountRate' => $request->discount_rate,
            'discountAmount' => $request->discount_amount,
            'finalPrice' => $request->final_price,
            'validityDate' => $validityDate
        ];

        $pdf = Pdf::loadView('representative.pdf.proposal', $data);
        $fileName = 'Proposta_NexShape_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
