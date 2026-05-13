<?php

namespace App\Services;

use App\Enums\PdfDocumentType;
use App\Enums\PdfValidationStatus;
use App\Jobs\SendPdfDocumentDeliveriesJob;
use App\Models\AcademyCompany;
use App\Models\AcademyUnit;
use App\Models\HistoricoPdf;
use App\Models\PdfTemplate;
use App\Models\User;
use App\Models\PdfNumberSequence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PdfIssuanceService
{
    public function __construct(
        private readonly PdfTemplateService $pdfTemplateService
    ) {}

    /**
     * Emite documento oficial: numeração, ficheiro, histórico, QR e filas de envio.
     *
     * @param  array<string, string|int|float|null>  $variables
     * @param  array<string, mixed>  $extraMetadata ex.: whatsapp_recipients => list<string>
     */
    public function issueOfficial(
        User $user,
        PdfTemplate $template,
        AcademyCompany $company,
        ?AcademyUnit $unit,
        array $variables,
        bool $queueDeliveries = true,
        array $extraMetadata = []
    ): HistoricoPdf {
        if ($template->academy_company_id !== null && (int) $template->academy_company_id !== (int) $company->id) {
            throw new \InvalidArgumentException('O modelo não pertence a esta empresa.');
        }
        if ($template->academy_unit_id !== null && $unit !== null && (int) $template->academy_unit_id !== (int) $unit->id) {
            throw new \InvalidArgumentException('O modelo não pertence a esta unidade.');
        }

        $type = $template->document_type instanceof PdfDocumentType
            ? $template->document_type
            : PdfDocumentType::from((string) $template->document_type);

        $year = (int) now()->year;
        $numero = $this->nextOfficialNumber($company, $type, $year);

        $codigo = $this->uniqueValidationCode();

        $validationUrl = $this->pdfTemplateService->validationUrl($codigo);
        $qrDataUri = $this->pdfTemplateService->pngDataUriForText($validationUrl, 130);

        $mergedVars = array_merge($variables, [
            'numero_oficial' => $numero,
            'codigo_validacao' => $codigo,
            'url_validacao' => $validationUrl,
            'data_emissao' => now()->format('d/m/Y H:i'),
        ]);

        $watermark = $company->watermarkConfig();
        $envelope = [
            'qr_data_uri' => $qrDataUri,
            'watermark' => $watermark !== [] ? $watermark : null,
            'signatures_html' => '',
        ];

        $binary = $this->pdfTemplateService->renderPdfBinary($template, $mergedVars, 'A4', 'portrait', $envelope);

        $disk = config('pdf.historico_disk', 'local');
        $dir = trim(config('pdf.historico_directory', 'historico-pdfs'), '/');
        $safeName = Str::slug($template->name).'_'.Str::lower($codigo).'.pdf';
        $relative = $dir.'/'.$company->id.'/'.now()->format('Y/m').'/'.$safeName;

        Storage::disk($disk)->put($relative, $binary);

        $ttlDays = (int) config('pdf.default_ttl_days', 0);
        $expiresAt = $ttlDays > 0 ? Carbon::now()->addDays($ttlDays) : null;

        $historico = HistoricoPdf::create([
            'academy_company_id' => $company->id,
            'academy_unit_id' => $unit?->id,
            'user_id' => $user->id,
            'pdf_template_id' => $template->id,
            'document_type' => $type->value,
            'numero_oficial' => $numero,
            'nome_arquivo' => $safeName,
            'caminho_arquivo' => $relative,
            'codigo_validacao' => $codigo,
            'validation_status' => PdfValidationStatus::Valid,
            'issued_at' => now(),
            'expires_at' => $expiresAt,
            'generation_status' => 'complete',
            'source_variables' => $mergedVars,
            'metadata' => array_merge([
                'validation_url' => $validationUrl,
            ], $extraMetadata),
            'ip_address' => request()->ip(),
        ]);

        $this->pdfTemplateService->logGeneration(
            $user,
            $template,
            $type,
            \App\Models\PdfGenerationLog::ACTION_DOWNLOAD,
            $safeName,
            'success',
            null,
            $historico->id
        );

        if ($queueDeliveries) {
            SendPdfDocumentDeliveriesJob::dispatch($historico->id);
        }

        return $historico;
    }

    public function persistRegeneratedFile(HistoricoPdf $historico): void
    {
        $binary = $this->pdfTemplateService->renderHistoricoBinary($historico);
        $disk = config('pdf.historico_disk', 'local');
        Storage::disk($disk)->put($historico->caminho_arquivo, $binary);
    }

    private function uniqueValidationCode(): string
    {
        do {
            $code = Str::lower(Str::random(24));
        } while (HistoricoPdf::query()->where('codigo_validacao', $code)->exists());

        return $code;
    }

    /**
     * Gera número único por empresa, tipo e ano (ex.: REC-2026-000001).
     */
    private function nextOfficialNumber(AcademyCompany $company, \App\Enums\PdfDocumentType $type, int $year): string
    {
        return DB::transaction(function () use ($company, $type, $year) {
            $row = PdfNumberSequence::query()
                ->where('academy_company_id', $company->id)
                ->where('tipo_documento', $type->value)
                ->where('ano', $year)
                ->lockForUpdate()
                ->first();

            if ($row === null) {
                $row = PdfNumberSequence::create([
                    'academy_company_id' => $company->id,
                    'tipo_documento' => $type->value,
                    'ano' => $year,
                    'sequencia_atual' => 0,
                ]);
            }

            $row->increment('sequencia_atual');
            $row->refresh();

            $seq = str_pad((string) $row->sequencia_atual, 6, '0', STR_PAD_LEFT);

            return $type->numberPrefix().'-'.$year.'-'.$seq;
        });
    }
}
