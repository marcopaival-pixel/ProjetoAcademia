<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessLandingController extends Controller
{
    /**
     * Exibe a landing page dedicada à Gestão Clínica (NexShape Business).
     */
    public function __invoke(Request $request): View
    {
        // Busca apenas os planos voltados para Clínicas/Academias
        // No sistema, eles estão associados à role 'clinic' ou 'academia'
        $businessPlans = Plan::where('is_active', true)
            ->where(function($query) {
                $query->where('type', 'clinic')
                      ->orWhereHas('roles', function($q) {
                          $q->whereIn('name', ['clinic', 'academia']);
                      });
            })
            ->with(['planFeatures'])
            ->get();

        return view('business', [
            'plans' => $businessPlans,
        ]);
    }
}
