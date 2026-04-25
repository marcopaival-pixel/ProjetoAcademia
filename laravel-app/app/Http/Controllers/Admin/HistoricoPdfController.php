<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PdfDocumentType;
use App\Enums\PdfValidationStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendPdfDocumentDeliveriesJob;
use App\Models\AcademyCompany;
use App\Models\HistoricoPdf;
use App\Models\PdfSignatureAuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HistoricoPdfController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if ($user === null || (! $user->isAdministrator() && ! $user->hasPermission('pdf.history.view'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $q = HistoricoPdf::query()->with(['user', 'company', 'unit', 'template'])->orderByDesc('id');

        if ($request->filled('empresa_id')) {
            $q->where('academy_company_id', (int) $request->input('empresa_id'));
        }
        if ($request->filled('usuario_id')) {
            $q->where('user_id', (int) $request->input('usuario_id'));
        }
        if ($request->filled('tipo_documento')) {
            $q->where('document_type', (string) $request->input('tipo_documento'));
        }
        if ($request->filled('validation_status')) {
            $q->where('validation_status', (string) $request->input('validation_status'));
        }
        if ($request->filled('from')) {
            $q->whereDate('issued_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('issued_at', '<=', $request->input('to'));
        }

        if (! $request->user()->isAdministrator()) {
            $cid = $request->user()->academy_company_id;
            if ($cid === null) {
                $q->whereNull('academy_company_id');
            } else {
                $q->where('academy_company_id', $cid);
            }
        }

        $items = $q->paginate(30)->withQueryString();

        return view('admin.pdf-suite.historico-index', [
            'items' => $items,
            'companies' => AcademyCompany::query()->orderBy('name')->get(),
            'documentTypes' => PdfDocumentType::cases(),
        ]);
    }

    public function download(Request $request, HistoricoPdf $historicoPdf): Response
    {
        $this->authorizeCompany($request->user(), $historicoPdf);

        $disk = config('pdf.historico_disk', 'local');
        if (! Storage::disk($disk)->exists($historicoPdf->caminho_arquivo)) {
            abort(404, 'Ficheiro não encontrado.');
        }

        return Storage::disk($disk)->download($historicoPdf->caminho_arquivo, $historicoPdf->nome_arquivo);
    }

    public function cancel(Request $request, HistoricoPdf $historicoPdf): RedirectResponse
    {
        if (! $request->user()->isAdministrator() && ! $request->user()->hasPermission('pdf.document.cancel')) {
            abort(403);
        }
        $this->authorizeCompany($request->user(), $historicoPdf);

        $historicoPdf->update(['validation_status' => PdfValidationStatus::Cancelled]);
        PdfSignatureAuditLog::create([
            'historico_pdf_id' => $historicoPdf->id,
            'user_id' => $request->user()->id,
            'evento' => 'documento_cancelado',
            'detalhe' => 'Validação invalidada administrativamente.',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Documento marcado como cancelado para validação.');
    }

    public function resend(Request $request, HistoricoPdf $historicoPdf): RedirectResponse
    {
        if (! $request->user()->isAdministrator()
            && ! $request->user()->hasPermission('pdf.delivery.email')
            && ! $request->user()->hasPermission('pdf.delivery.whatsapp')) {
            abort(403);
        }
        $this->authorizeCompany($request->user(), $historicoPdf);

        SendPdfDocumentDeliveriesJob::dispatch($historicoPdf->id);

        return back()->with('success', 'Reenvio agendado (e-mail / WhatsApp conforme modelo).');
    }

    private function authorizeCompany(?\App\Models\User $user, HistoricoPdf $historicoPdf): void
    {
        if ($user === null) {
            abort(403);
        }
        if ($user->isAdministrator()) {
            return;
        }
        if ((int) $user->academy_company_id !== (int) $historicoPdf->academy_company_id) {
            abort(403, 'Documento pertence a outra empresa.');
        }
    }
}
