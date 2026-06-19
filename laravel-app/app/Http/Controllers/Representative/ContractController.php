<?php

namespace App\Http\Controllers\Representative;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = \App\Models\Contract::where('representative_id', auth()->id())->latest()->paginate(20);
        return view('representative.contracts.index', compact('contracts'));
    }

    public function show(\App\Models\Contract $contract)
    {
        $this->authorize('view', $contract);
        return view('representative.contracts.show', compact('contract'));
    }
}
