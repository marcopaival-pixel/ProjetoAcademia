<?php

namespace App\Http\Controllers\Representative;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Clinic::where('representative_id', auth()->id());

        // Filtros
        if ($request->filled('status')) {
            $status = $request->get('status');
            if (in_array($status, ['ativa', 'inativa', 'inadimplente'])) {
                $query->where('sale_status', $status);
            } elseif ($status === 'novas_vendas') {
                $query->where('sale_date', '>=', now()->subDays(30));
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->get('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->get('end_date'));
        }

        $clinics = $query->latest('sale_date')->paginate(20);
        
        return view('representative.clinics.index', compact('clinics'));
    }

    public function show(\App\Models\Clinic $clinic)
    {
        $this->authorize('view', $clinic);
        return view('representative.clinics.show', compact('clinic'));
    }
}
