<?php

namespace App\Models;

use App\Enums\PdfDocumentType;
use App\Enums\PdfValidationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistoricoPdf extends Model
{
    use Traits\FiltersByProfessional, Traits\BelongsToCompany;

    protected $table = 'historico_pdfs';

    protected $fillable = [
        'academy_company_id',
        'academy_unit_id',
        'user_id',
        'pdf_template_id',
        'document_type',
        'related_document_type',
        'related_document_id',
        'numero_oficial',
        'nome_arquivo',
        'caminho_arquivo',
        'codigo_validacao',
        'validation_status',
        'issued_at',
        'expires_at',
        'generation_status',
        'source_variables',
        'metadata',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => PdfDocumentType::class,
            'validation_status' => PdfValidationStatus::class,
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'source_variables' => 'array',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<AcademyCompany, HistoricoPdf>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }

    /**
     * @return BelongsTo<AcademyUnit, HistoricoPdf>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(AcademyUnit::class, 'academy_unit_id');
    }

    /**
     * @return BelongsTo<User, HistoricoPdf>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<PdfTemplate, HistoricoPdf>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(PdfTemplate::class, 'pdf_template_id');
    }

    /**
     * @return HasMany<PdfSignature, HistoricoPdf>
     */
    public function signatures(): HasMany
    {
        return $this->hasMany(PdfSignature::class, 'historico_pdf_id');
    }

    /**
     * @return HasMany<PdfDeliveryLog, HistoricoPdf>
     */
    public function deliveryLogs(): HasMany
    {
        return $this->hasMany(PdfDeliveryLog::class, 'historico_pdf_id');
    }

    public function resolvedValidationStatus(): PdfValidationStatus
    {
        if ($this->validation_status === PdfValidationStatus::Cancelled) {
            return PdfValidationStatus::Cancelled;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return PdfValidationStatus::Expired;
        }

        return PdfValidationStatus::Valid;
    }

    public function scopeForCompany($query, ?int $companyId)
    {
        if ($companyId === null) {
            return $query->whereNull('academy_company_id');
        }

        return $query->where('academy_company_id', $companyId);
    }
}
