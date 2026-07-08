<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadCaptureController extends Controller
{
    /**
     * Recebe leads de Landing Pages externas (ex: RD Station, Webflow)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'required|string|max:20',
            'empresa' => 'nullable|string|max:255',
            'numero_alunos' => 'nullable|string|max:50', // Ex: "Até 200"
            'lgpd_consent' => 'required|accepted', // Validação estrita LGPD
            'origem' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lead = Lead::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'empresa' => $request->empresa,
            'origem' => $request->origem ?? 'API Landing Page',
            'observacao' => 'Número de alunos relatado: ' . ($request->numero_alunos ?? 'Não informado'),
            'status' => 'novo',
        ]);

        return response()->json([
            'message' => 'Lead capturado com sucesso.',
            'lead_id' => $lead->id,
        ], 201);
    }
}
