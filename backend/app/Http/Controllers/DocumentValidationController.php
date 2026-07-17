<?php

namespace App\Http\Controllers;

use App\Enums\PdfValidationStatus;
use App\Models\HistoricoPdf;
use Illuminate\View\View;

class DocumentValidationController extends Controller
{
    public function show(string $codigo): View
    {
        $historico = HistoricoPdf::query()
            ->with(['company', 'unit', 'template'])
            ->where('codigo_validacao', $codigo)
            ->firstOrFail();

        $status = $historico->resolvedValidationStatus();

        return view('public.document-validation', [
            'historico' => $historico,
            'status' => $status,
            'statusLabel' => $status->label(),
            'isValid' => $status === PdfValidationStatus::Valid,
        ]);
    }
}
