<?php

namespace App\Services;

use App\Enums\PdfDocumentType;
use App\Models\HistoricoPdf;
use App\Models\PdfGenerationLog;
use App\Models\PdfSignature;
use App\Models\PdfTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfTemplateService
{
    public function __construct(
        private readonly DompdfPdfService $dompdf
    ) {}

    /**
     * @param  array<string, string|int|float|null>  $variables
     */
    public function interpolate(string $html, array $variables): string
    {
        return (string) preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',
            function (array $m) use ($variables): string {
                $key = $m[1];
                if (! array_key_exists($key, $variables)) {
                    return $m[0];
                }
                $val = $variables[$key];
                if ($val === null) {
                    return '';
                }

                return e((string) $val);
            },
            $html
        );
    }

    public function logoDataUri(?string $logoPath): ?string
    {
        if ($logoPath === null || $logoPath === '') {
            return null;
        }
        if (! Storage::disk('public')->exists($logoPath)) {
            return null;
        }
        $bytes = Storage::disk('public')->get($logoPath);
        $ext = strtolower((string) pathinfo($logoPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };

        return 'data:'.$mime.';base64,'.base64_encode($bytes);
    }

    /**
     * @param  array<string, string|int|float|null>  $variables
     * @param  array{
     *     qr_data_uri?: string|null,
     *     footer_html?: string|null,
     *     watermark?: array{text?: string, opacity?: float, position?: string, image_path?: string|null}|null,
     *     signatures_html?: string|null
     * }  $envelope
     */
    public function buildHtml(PdfTemplate $template, array $variables, array $envelope = []): string
    {
        $type = $template->document_type instanceof PdfDocumentType
            ? $template->document_type
            : PdfDocumentType::from((string) $template->document_type);

        $merged = array_merge(
            $type->sampleVariables(),
            $variables
        );
        $merged['titulo_documento'] = $merged['titulo_documento'] ?? $template->name;

        $bodyHtml = $this->interpolate($template->html_body, $merged);
        $logoUri = $this->logoDataUri($template->logo_path);

        $primary = $template->primary_color ?: '#1e293b';
        $secondary = $template->secondary_color ?: '#64748b';
        $accent = $template->accent_color ?: '#3b82f6';

        $extraCss = $template->css_extra ? $this->interpolate($template->css_extra, $merged) : '';

        $logoBlock = '';
        if ($logoUri !== null) {
            $logoBlock = '<div class="pdf-logo-wrap"><img src="'.e($logoUri).'" alt="Logo" class="pdf-logo" /></div>';
        }

        $footerHtml = $envelope['footer_html'] ?? null;
        if ($footerHtml === null && $template->footer_html) {
            $footerHtml = $this->interpolate($template->footer_html, $merged);
        }

        $watermark = $envelope['watermark'] ?? null;
        $watermarkBlock = $this->buildWatermarkBlock($watermark, $merged);

        $qrUri = $envelope['qr_data_uri'] ?? null;
        $qrBlock = '';
        if (is_string($qrUri) && $qrUri !== '') {
            $qrBlock = '<div class="pdf-qr"><p class="pdf-qr-label">Validação</p><img class="pdf-qr-img" src="'.e($qrUri).'" alt="QR" /></div>';
        }

        $signaturesHtml = is_string($envelope['signatures_html'] ?? null) ? $envelope['signatures_html'] : '';

        $footerSection = $footerHtml ? '<div class="pdf-footer">'.$footerHtml.'</div>' : '';
        $tailSection = '<table class="pdf-tail" style="width:100%;margin-top:20px;"><tr><td style="width:140px;vertical-align:top;">'.$qrBlock.'</td><td style="vertical-align:bottom;">'.$signaturesHtml.'</td></tr></table>';

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        :root {
            --pdf-primary: {$primary};
            --pdf-secondary: {$secondary};
            --pdf-accent: {$accent};
        }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            color: #0f172a;
            margin: 0;
            padding: 24px;
            line-height: 1.45;
        }
        .pdf-watermark-layer {
            position: fixed;
            top: 38%;
            left: 10%;
            width: 80%;
            text-align: center;
            font-size: 42px;
            font-weight: bold;
            color: #94a3b8;
            z-index: 0;
            pointer-events: none;
        }
        .pdf-watermark-layer img {
            max-width: 280px;
            opacity: inherit;
        }
        .pdf-main-wrap { position: relative; z-index: 1; }
        .pdf-shell-header {
            border-bottom: 2px solid var(--pdf-accent);
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .pdf-logo-wrap { margin-bottom: 8px; }
        .pdf-logo { max-height: 56px; max-width: 200px; }
        h1, h2, h3 { color: var(--pdf-primary); margin: 0.6em 0 0.3em; }
        .pdf-body { margin-top: 8px; }
        .pdf-footer { margin-top: 28px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 9pt; color: #64748b; }
        .pdf-qr { text-align: center; }
        .pdf-qr-label { font-size: 7pt; text-transform: uppercase; color: #64748b; margin: 0 0 4px; }
        .pdf-qr-img { width: 120px; height: 120px; }
        .pdf-signature-pad { display: inline-block; margin-right: 24px; text-align: center; vertical-align: top; }
        .pdf-signature-pad img { max-height: 64px; max-width: 160px; }
        .pdf-signature-role { font-size: 7pt; color: #64748b; text-transform: uppercase; margin-top: 4px; }
        {$extraCss}
    </style>
</head>
<body>
    {$watermarkBlock}
    <div class="pdf-main-wrap">
        <div class="pdf-shell-header">
            {$logoBlock}
        </div>
        <div class="pdf-body">{$bodyHtml}</div>
        {$footerSection}
        {$tailSection}
    </div>
</body>
</html>
HTML;
    }

    /**
     * @param  array<string, mixed>|null  $watermark
     * @param  array<string, string|int|float|null>  $merged
     */
    private function buildWatermarkBlock(?array $watermark, array $merged): string
    {
        if ($watermark === null || $watermark === []) {
            return '';
        }
        
        $opacity = isset($watermark['opacity']) ? max(0.01, min(1.0, (float) $watermark['opacity'])) : 0.12;
        $rotate = isset($watermark['rotate']) ? (int) $watermark['rotate'] : -32;
        $scale = isset($watermark['scale']) ? (float) $watermark['scale'] : 1.0;
        
        $text = isset($watermark['text']) ? (string) $watermark['text'] : '';
        $text = $text !== '' ? $this->interpolate($text, $merged) : '';
        
        $imagePath = $watermark['image_path'] ?? null;
        $imgUri = is_string($imagePath) && $imagePath !== '' ? $this->logoDataUri($imagePath) : null;

        $style = sprintf(
            'opacity: %f; transform: rotate(%ddeg) scale(%f);',
            $opacity,
            $rotate,
            $scale
        );

        if ($text !== '') {
            return '<div class="pdf-watermark-layer" style="'.$style.'">'.e($text).'</div>';
        }
        
        if ($imgUri !== null) {
            return '<div class="pdf-watermark-layer" style="'.$style.'"><img src="'.e($imgUri).'" alt="" /></div>';
        }

        return '';
    }

    /**
     * @param  iterable<PdfSignature>  $signatures
     */
    public function buildSignaturesHtml(iterable $signatures): string
    {
        $html = '';
        foreach ($signatures as $sig) {
            $uri = $this->logoDataUri($sig->imagem_assinatura);
            if ($uri === null) {
                continue;
            }
            $roleLabel = $sig->tipo_assinatura instanceof \App\Enums\PdfSignatureRole
                ? $sig->tipo_assinatura->label()
                : (string) $sig->tipo_assinatura;
            $html .= '<div class="pdf-signature-pad"><img src="'.e($uri).'" alt="Assinatura" /><div class="pdf-signature-role">'.e($roleLabel).'</div></div>';
        }

        return $html;
    }

    /**
     * @param  array<string, string|int|float|null>  $variables
     * @param  array<string, mixed>  $envelope
     * @return non-empty-string
     */
    public function renderPdfBinary(
        PdfTemplate $template,
        array $variables,
        string $paper = 'A4',
        string $orientation = 'portrait',
        array $envelope = []
    ): string {
        $html = $this->buildHtml($template, $variables, $envelope);

        return $this->dompdf->render($html, $paper, $orientation, false, 'DejaVu Sans');
    }

    public function clearDefaultForType(PdfDocumentType $type, ?int $companyId, ?int $unitId): void
    {
        $q = PdfTemplate::query()->forType($type);
        if ($companyId === null) {
            $q->whereNull('academy_company_id');
        } else {
            $q->where('academy_company_id', $companyId);
        }
        if ($unitId === null) {
            $q->whereNull('academy_unit_id');
        } else {
            $q->where('academy_unit_id', $unitId);
        }
        $q->update(['is_default' => false]);
    }

    public function renderHistoricoBinary(HistoricoPdf $historico): string
    {
        $template = $historico->template;
        if ($template === null) {
            throw new \RuntimeException('Modelo PDF não encontrado para regenerar o documento.');
        }
        $variables = is_array($historico->source_variables) ? $historico->source_variables : [];
        $company = $historico->company;
        $watermark = $company ? $company->watermarkConfig() : [];

        $envelope = [
            'watermark' => $watermark !== [] ? $watermark : null,
            'signatures_html' => $this->buildSignaturesHtml($historico->signatures()->get()),
        ];

        if ($historico->codigo_validacao) {
            $qr = app(PdfQrService::class)->validationUrl($historico->codigo_validacao);
            $envelope['qr_data_uri'] = app(PdfQrService::class)->pngDataUriForText($qr, 130);
        }

        return $this->renderPdfBinary($template, $variables, 'A4', 'portrait', $envelope);
    }

    /**
     * @param  array<string, string|int|float|null>  $variables
     */
    public function logGeneration(
        ?User $user,
        ?PdfTemplate $template,
        PdfDocumentType|string $documentType,
        string $action,
        string $filename,
        string $status,
        ?string $errorMessage = null,
        ?int $historicoPdfId = null
    ): void {
        $doc = $documentType instanceof PdfDocumentType ? $documentType->value : $documentType;

        PdfGenerationLog::create([
            'user_id' => $user?->id,
            'pdf_template_id' => $template?->id,
            'historico_pdf_id' => $historicoPdfId,
            'document_type' => $doc,
            'template_name' => $template?->name,
            'action' => $action,
            'filename' => Str::limit($filename, 240),
            'status' => $status,
            'error_message' => $errorMessage !== null ? Str::limit($errorMessage, 2000) : null,
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 500),
        ]);
    }
}
