<?php

namespace App\Http\Controllers;

use App\Services\ReportValidationService;
use Illuminate\Http\Request;

class ReportValidationController extends Controller
{
    public function validate(Request $request, ReportValidationService $service)
    {
        $doc = (string) $request->query('doc');
        $v = (int) $request->query('v');
        $token = (string) $request->query('token');
        $t = $request->query('t');

        if (!$doc || !$v || !$token || !$t) {
            return view('report-validation.invalid', ['message' => 'Parâmetros de validação incompletos.']);
        }

        $isValid = $service->validate($doc, $v, $token, $t);

        if (!$isValid) {
            return view('report-validation.invalid', ['message' => 'O documento fornecido é INVÁLIDO ou foi adulterado.']);
        }

        // Se for válido, buscamos os dados para exibir um comprovante de autenticidade
        $report = \App\Models\GeneratedReport::where('document_id', $doc)
            ->where('version', $v)
            ->with('user')
            ->first();

        return view('report-validation.success', [
            'report' => $report,
            'user' => $report->user
        ]);
    }
}
