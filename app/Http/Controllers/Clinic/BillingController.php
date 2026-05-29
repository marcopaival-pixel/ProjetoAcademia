<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->academyCompany;

        if (!$company) {
            return back()->with('error', 'Você não está vinculado a uma clínica.');
        }

        $subscription = $company->activeSubscription();
        
        // Se não houver assinatura corporativa, talvez a clínica esteja em trial ou usando assinaturas individuais
        // Para este MVP de escala, vamos focar no modelo consolidado
        
        $teamCount = $company->professionals()->count();
        
        $plans = Plan::where('is_corporate', true)->where('status', 'active')->get();

        return view('clinic.billing', compact('company', 'subscription', 'teamCount', 'plans'));
    }
}
