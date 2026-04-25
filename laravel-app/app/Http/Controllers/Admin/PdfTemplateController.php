<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PdfDocumentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PdfTemplateRequest;
use App\Models\AcademyCompany;
use App\Models\AcademyUnit;
use App\Models\PdfGenerationLog;
use App\Models\PdfTemplate;
use App\Services\PdfTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PdfTemplateController extends Controller
{
    public function __construct(
        private readonly PdfTemplateService $pdfTemplateService
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', PdfTemplate::class);

        $q = PdfTemplate::query();
        if (! auth()->user()->isAdministrator() && auth()->user()->academy_company_id) {
            $q->forTenant((int) auth()->user()->academy_company_id);
        }
        $templates = $q->with(['company', 'unit'])
            ->orderBy('document_type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (PdfTemplate $t) => $t->document_type->value);

        return view('admin.pdf-templates.index', [
            'templatesByType' => $templates,
            'documentTypes' => PdfDocumentType::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', PdfTemplate::class);

        return view('admin.pdf-templates.form', [
            'template' => new PdfTemplate([
                'is_active' => true,
                'primary_color' => '#1e293b',
                'html_body' => $this->defaultHtmlStub(),
            ]),
            'documentTypes' => PdfDocumentType::cases(),
            'companies' => AcademyCompany::query()->where('is_active', true)->orderBy('name')->get(),
            'units' => AcademyUnit::query()->where('is_active', true)->with('company')->orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function store(PdfTemplateRequest $request): RedirectResponse
    {
        $this->authorize('create', PdfTemplate::class);

        $data = $this->validatedPayload($request);
        $type = PdfDocumentType::from($data['document_type']);

        if (! empty($data['is_default'])) {
            $this->pdfTemplateService->clearDefaultForType(
                $type,
                $data['academy_company_id'] ?? null,
                $data['academy_unit_id'] ?? null
            );
        }

        $template = PdfTemplate::create($data);

        if ($request->hasFile('logo')) {
            $template->logo_path = $this->storeLogo($request, $template->id);
            $template->save();
        }

        return redirect()
            ->route('admin.pdf-templates.edit', $template)
            ->with('success', 'Modelo de PDF criado.');
    }

    public function edit(PdfTemplate $pdfTemplate): View
    {
        $this->authorize('update', $pdfTemplate);

        return view('admin.pdf-templates.form', [
            'template' => $pdfTemplate,
            'documentTypes' => PdfDocumentType::cases(),
            'companies' => AcademyCompany::query()->where('is_active', true)->orderBy('name')->get(),
            'units' => AcademyUnit::query()->where('is_active', true)->with('company')->orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(PdfTemplateRequest $request, PdfTemplate $pdfTemplate): RedirectResponse
    {
        $this->authorize('update', $pdfTemplate);

        $data = $this->validatedPayload($request);
        $type = PdfDocumentType::from($data['document_type']);

        if (! empty($data['is_default'])) {
            $this->pdfTemplateService->clearDefaultForType(
                $type,
                $data['academy_company_id'] ?? null,
                $data['academy_unit_id'] ?? null
            );
        }

        if ($request->boolean('remove_logo') && $pdfTemplate->logo_path) {
            Storage::disk('public')->delete($pdfTemplate->logo_path);
            $data['logo_path'] = null;
        }

        $pdfTemplate->update($data);

        if ($request->hasFile('logo')) {
            if ($pdfTemplate->logo_path) {
                Storage::disk('public')->delete($pdfTemplate->logo_path);
            }
            $pdfTemplate->logo_path = $this->storeLogo($request, $pdfTemplate->id);
            $pdfTemplate->save();
        }

        return redirect()
            ->route('admin.pdf-templates.edit', $pdfTemplate)
            ->with('success', 'Modelo atualizado.');
    }

    public function destroy(PdfTemplate $pdfTemplate): RedirectResponse
    {
        $this->authorize('delete', $pdfTemplate);

        if ($pdfTemplate->logo_path) {
            Storage::disk('public')->delete($pdfTemplate->logo_path);
        }
        $pdfTemplate->delete();

        return redirect()
            ->route('admin.pdf-templates.index')
            ->with('success', 'Modelo removido.');
    }

    public function toggleActive(PdfTemplate $pdfTemplate): RedirectResponse
    {
        $this->authorize('update', $pdfTemplate);

        $next = ! $pdfTemplate->is_active;
        $pdfTemplate->update(['is_active' => $next]);

        return back()->with('success', $next ? 'Modelo ativado.' : 'Modelo desativado.');
    }

    public function duplicate(PdfTemplate $pdfTemplate): RedirectResponse
    {
        $this->authorize('create', PdfTemplate::class);

        $clone = $pdfTemplate->replicate();
        $clone->name = $pdfTemplate->name.' (cópia)';
        $clone->is_default = false;
        $clone->duplicated_from_id = $pdfTemplate->id;
        $clone->save();

        return redirect()
            ->route('admin.pdf-templates.edit', $clone)
            ->with('success', 'Modelo duplicado.');
    }

    /**
     * Pré-visualização inline (PDF no browser).
     */
    public function preview(Request $request, PdfTemplate $pdfTemplate): Response
    {
        $this->authorize('view', $pdfTemplate);

        $variables = $this->variablesFromRequest($request);

        try {
            $binary = $this->pdfTemplateService->renderPdfBinary($pdfTemplate, $variables);
            $filename = $this->safeFilename($pdfTemplate->name).'_preview.pdf';

            $this->pdfTemplateService->logGeneration(
                $request->user(),
                $pdfTemplate,
                $pdfTemplate->document_type,
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
                $pdfTemplate,
                $pdfTemplate->document_type,
                PdfGenerationLog::ACTION_PREVIEW,
                'preview-failed.pdf',
                'failed',
                $e->getMessage()
            );
            throw $e;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(PdfTemplateRequest $request): array
    {
        $validated = $request->validated();

        return [
            'academy_company_id' => $validated['academy_company_id'] ?? null,
            'academy_unit_id' => $validated['academy_unit_id'] ?? null,
            'name' => $validated['name'],
            'document_type' => $validated['document_type'],
            'description' => $validated['description'] ?? null,
            'html_body' => $validated['html_body'],
            'css_extra' => $validated['css_extra'] ?? null,
            'primary_color' => $validated['primary_color'] ?? '#1e293b',
            'secondary_color' => $validated['secondary_color'] ?? null,
            'accent_color' => $validated['accent_color'] ?? null,
            'footer_html' => $validated['footer_html'] ?? null,
            'auto_email_enabled' => $request->boolean('auto_email_enabled'),
            'auto_email_recipients' => $this->parseEmailList($request->input('auto_email_recipients')),
            'auto_whatsapp_enabled' => $request->boolean('auto_whatsapp_enabled'),
            'auto_whatsapp_recipients' => $this->parsePhoneLines($request->input('auto_whatsapp_recipients')),
            'whatsapp_message_template' => $validated['whatsapp_message_template'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];
    }

    /**
     * @return list<string>|null
     */
    private function parseEmailList(mixed $raw): ?array
    {
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }
        $parts = preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $out = [];
        foreach ($parts as $p) {
            $p = trim((string) $p);
            if ($p !== '' && filter_var($p, FILTER_VALIDATE_EMAIL)) {
                $out[] = $p;
            }
        }

        return $out !== [] ? array_values(array_unique($out)) : null;
    }

    /**
     * @return list<string>|null
     */
    private function parsePhoneLines(mixed $raw): ?array
    {
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }
        $lines = preg_split('/\R+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $d = preg_replace('/\D+/', '', (string) $line);
            if ($d !== '') {
                $out[] = $d;
            }
        }

        return $out !== [] ? array_values(array_unique($out)) : null;
    }

    private function storeLogo(PdfTemplateRequest $request, int $templateId): string
    {
        $file = $request->file('logo');
        $ext = $file->getClientOriginalExtension() ?: 'png';
        $path = $file->storeAs(
            'pdf-template-logos',
            $templateId.'_'.Str::uuid().'.'.$ext,
            'public'
        );

        return $path;
    }

    private function defaultHtmlStub(): string
    {
        return <<<'HTML'
<h1>{{ titulo_documento }}</h1>
<p>Este é um modelo inicial. Edite o HTML e use variáveis no formato <strong>{{ nome_variavel }}</strong>.</p>
<p>Aluno: {{ aluno_nome }}</p>
<p>Data: {{ data_emissao }}</p>
HTML;
    }

    /**
     * @return array<string, string|int|float|null>
     */
    private function variablesFromRequest(Request $request): array
    {
        $raw = (string) $request->input('variables_json', '');
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
