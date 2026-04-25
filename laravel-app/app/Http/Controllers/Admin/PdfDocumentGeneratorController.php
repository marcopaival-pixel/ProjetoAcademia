<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PdfDocumentType;
use App\Http\Controllers\Controller;
use App\Models\AcademyCompany;
use App\Models\AcademyUnit;
use App\Models\PdfGenerationLog;
use App\Models\PdfTemplate;
use App\Services\PdfIssuanceService;
use App\Services\PdfTemplateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PdfDocumentGeneratorController extends Controller
{
    public function __construct(
        private readonly PdfTemplateService $pdfTemplateService,
        private readonly PdfIssuanceService $pdfIssuanceService
    ) {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (
                $user === null
                || (! $user->isAdministrator()
                    && ! $user->hasPermission('pdf.templates.manage')
                    && ! $user->hasPermission('pdf.documents.generate'))
            ) {
                abort(403, 'Sem permissão para gerar documentos PDF.');
            }

            return $next($request);
        });
    }

    public function create(): View
    {
        $tq = PdfTemplate::query()->active();
        if (! auth()->user()->isAdministrator() && auth()->user()->academy_company_id) {
            $tq->forTenant((int) auth()->user()->academy_company_id);
        }
        $templates = $tq->orderBy('document_type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $defaults = [];
        foreach (PdfDocumentType::cases() as $case) {
            $dq = PdfTemplate::query()->active()->forType($case);
            if (! auth()->user()->isAdministrator() && auth()->user()->academy_company_id) {
                $dq->forTenant((int) auth()->user()->academy_company_id);
            }
            $defaults[$case->value] = (clone $dq)->where('is_default', true)->first()
                ?? (clone $dq)->orderBy('sort_order')->first();
        }

        return view('admin.pdf-templates.generate', [
            'templates' => $templates,
            'documentTypes' => PdfDocumentType::cases(),
            'defaultsByType' => $defaults,
            'companies' => AcademyCompany::query()->where('is_active', true)->orderBy('name')->get(),
            'units' => AcademyUnit::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function preview(Request $request): Response
    {
        $request->validate([
            'pdf_template_id' => ['required', 'integer', 'exists:pdf_templates,id'],
            'variables_json' => ['nullable', 'string', 'max:100000'],
        ]);

        $template = PdfTemplate::query()->whereKey($request->integer('pdf_template_id'))->firstOrFail();
        if (! $template->is_active) {
            abort(422, 'Este modelo está inativo.');
        }

        $variables = $this->decodeVariables((string) $request->input('variables_json', ''));

        try {
            $binary = $this->pdfTemplateService->renderPdfBinary($template, $variables);
            $filename = $this->safeFilename($template->name).'_preview.pdf';

            $this->pdfTemplateService->logGeneration(
                $request->user(),
                $template,
                $template->document_type,
                PdfGenerationLog::ACTION_PREVIEW,
                $filename,
                'success'
            );

            return response($binary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]);
        } catch (\Throwable $e) {
            $this->pdfTemplateService->logGeneration(
                $request->user(),
                $template,
                $template->document_type,
                PdfGenerationLog::ACTION_PREVIEW,
                'preview-failed.pdf',
                'failed',
                $e->getMessage()
            );
            throw $e;
        }
    }

    public function download(Request $request): Response
    {
        $request->validate([
            'pdf_template_id' => ['required', 'integer', 'exists:pdf_templates,id'],
            'variables_json' => ['nullable', 'string', 'max:100000'],
            'register_official' => ['sometimes', 'boolean'],
            'academy_company_id' => ['nullable', 'integer', 'exists:academy_companies,id'],
            'academy_unit_id' => ['nullable', 'integer', 'exists:academy_units,id'],
            'whatsapp_recipients_text' => ['nullable', 'string', 'max:2000'],
        ]);

        $template = PdfTemplate::query()->whereKey($request->integer('pdf_template_id'))->firstOrFail();
        if (! $template->is_active) {
            abort(422, 'Este modelo está inativo.');
        }

        $variables = $this->decodeVariables((string) $request->input('variables_json', ''));

        try {
            if ($request->boolean('register_official')) {
                $company = AcademyCompany::query()->whereKey((int) $request->input('academy_company_id'))->firstOrFail();
                $this->assertCompanyAccess($request, $company);
                $unit = null;
                if ($request->filled('academy_unit_id')) {
                    $unit = AcademyUnit::query()
                        ->whereKey((int) $request->input('academy_unit_id'))
                        ->where('academy_company_id', $company->id)
                        ->firstOrFail();
                }
                $phones = $this->parsePhones((string) $request->input('whatsapp_recipients_text', ''));
                $meta = $phones !== [] ? ['whatsapp_recipients' => $phones] : [];

                $historico = $this->pdfIssuanceService->issueOfficial(
                    $request->user(),
                    $template,
                    $company,
                    $unit,
                    $variables,
                    true,
                    $meta
                );

                $disk = config('pdf.historico_disk', 'local');

                return Storage::disk($disk)->download($historico->caminho_arquivo, $historico->nome_arquivo);
            }

            $binary = $this->pdfTemplateService->renderPdfBinary($template, $variables);
            $filename = $this->safeFilename($template->name).'_'.now()->format('Y-m-d_His').'.pdf';

            $this->pdfTemplateService->logGeneration(
                $request->user(),
                $template,
                $template->document_type,
                PdfGenerationLog::ACTION_DOWNLOAD,
                $filename,
                'success'
            );

            return response($binary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Throwable $e) {
            $this->pdfTemplateService->logGeneration(
                $request->user(),
                $template,
                $template->document_type,
                PdfGenerationLog::ACTION_DOWNLOAD,
                'download-failed.pdf',
                'failed',
                $e->getMessage()
            );
            throw $e;
        }
    }

    private function assertCompanyAccess(Request $request, AcademyCompany $company): void
    {
        $user = $request->user();
        if ($user->isAdministrator()) {
            return;
        }
        if ((int) $user->academy_company_id !== (int) $company->id) {
            abort(403, 'Empresa inválida para o seu utilizador.');
        }
    }

    /**
     * @return list<string>
     */
    private function parsePhones(string $raw): array
    {
        if (trim($raw) === '') {
            return [];
        }
        $lines = preg_split('/\R+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $d = preg_replace('/\D+/', '', (string) $line);
            if ($d !== '') {
                $out[] = $d;
            }
        }

        return array_values(array_unique($out));
    }

    /**
     * @return array<string, string|int|float|null>
     */
    private function decodeVariables(string $raw): array
    {
        if ($raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [];
        }
        $out = [];
        foreach ($decoded as $k => $v) {
            if (! is_string($k) || ! preg_match('/^[a-zA-Z0-9_]+$/', $k)) {
                continue;
            }
            if ($v === null) {
                $out[$k] = null;
            } elseif (is_scalar($v)) {
                $out[$k] = $v;
            }
        }

        return $out;
    }

    private function safeFilename(string $name): string
    {
        $s = Str::slug($name);

        return $s !== '' ? $s : 'documento';
    }
}
