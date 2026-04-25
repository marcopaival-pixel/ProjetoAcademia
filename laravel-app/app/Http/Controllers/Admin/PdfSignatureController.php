<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PdfSignatureMode;
use App\Enums\PdfSignatureRole;
use App\Http\Controllers\Controller;
use App\Models\HistoricoPdf;
use App\Models\PdfSignature;
use App\Models\PdfSignatureAuditLog;
use App\Services\PdfIssuanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PdfSignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if ($user === null || (! $user->isAdministrator() && ! $user->hasPermission('pdf.document.sign'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function store(Request $request, HistoricoPdf $historicoPdf, PdfIssuanceService $issuance): RedirectResponse
    {
        $this->authorizeHistorico($request, $historicoPdf);

        $roleValues = array_map(fn (PdfSignatureRole $r) => $r->value, PdfSignatureRole::cases());
        $modeValues = array_map(fn (PdfSignatureMode $m) => $m->value, PdfSignatureMode::cases());

        $validated = $request->validate([
            'tipo_assinatura' => ['required', 'string', 'in:'.implode(',', $roleValues)],
            'modo' => ['required', 'string', 'in:'.implode(',', $modeValues)],
            'imagem' => ['required', 'image', 'max:4096', 'mimes:png,jpg,jpeg,gif,webp'],
        ]);

        $path = $request->file('imagem')->storeAs(
            'pdf-signatures',
            $historicoPdf->id.'_'.Str::uuid().'.'.$request->file('imagem')->getClientOriginalExtension(),
            'public'
        );

        PdfSignature::create([
            'historico_pdf_id' => $historicoPdf->id,
            'user_id' => $request->user()->id,
            'tipo_assinatura' => PdfSignatureRole::from($validated['tipo_assinatura']),
            'modo' => PdfSignatureMode::from($validated['modo']),
            'imagem_assinatura' => $path,
            'ip_address' => $request->ip(),
            'data_assinatura' => now(),
        ]);

        PdfSignatureAuditLog::create([
            'historico_pdf_id' => $historicoPdf->id,
            'user_id' => $request->user()->id,
            'evento' => 'assinatura_registada',
            'detalhe' => 'Papel: '.$validated['tipo_assinatura'],
            'ip_address' => $request->ip(),
        ]);

        $issuance->persistRegeneratedFile($historicoPdf->fresh(['signatures', 'template', 'company']));

        return back()->with('success', 'Assinatura registada e PDF atualizado.');
    }

    private function authorizeHistorico(Request $request, HistoricoPdf $historicoPdf): void
    {
        $user = $request->user();
        if ($user->isAdministrator()) {
            return;
        }
        if ((int) $user->academy_company_id !== (int) $historicoPdf->academy_company_id) {
            abort(403);
        }
    }
}
