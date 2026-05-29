<?php

namespace App\Models;

use App\Enums\PdfDocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PdfTemplate extends Model
{
    use Traits\BelongsToCompany;
    protected $fillable = [
        'academy_company_id',
        'academy_unit_id',
        'name',
        'document_type',
        'description',
        'html_body',
        'css_extra',
        'logo_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'footer_html',
        'auto_email_enabled',
        'auto_email_recipients',
        'auto_whatsapp_enabled',
        'whatsapp_message_template',
        'auto_whatsapp_recipients',
        'duplicated_from_id',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => PdfDocumentType::class,
            'auto_email_enabled' => 'boolean',
            'auto_email_recipients' => 'array',
            'auto_whatsapp_recipients' => 'array',
            'auto_whatsapp_enabled' => 'boolean',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(AcademyUnit::class, 'academy_unit_id');
    }

    public function duplicatedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'duplicated_from_id');
    }

    /**
     * @return HasMany<PdfGenerationLog, PdfTemplate>
     */
    public function generationLogs(): HasMany
    {
        return $this->hasMany(PdfGenerationLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForType($query, PdfDocumentType|string $type)
    {
        $value = $type instanceof PdfDocumentType ? $type->value : $type;

        return $query->where('document_type', $value);
    }

    /**
     * Modelos globais (legado) ou da empresa indicada.
     */
    public function scopeForTenant($query, ?int $academyCompanyId)
    {
        return $query->where(function ($q) use ($academyCompanyId) {
            $q->whereNull('academy_company_id');
            if ($academyCompanyId !== null) {
                $q->orWhere('academy_company_id', $academyCompanyId);
            }
        });
    }

    public function scopeForUnit($query, ?int $unitId)
    {
        if ($unitId === null) {
            return $query->whereNull('academy_unit_id');
        }

        return $query->where(function ($q) use ($unitId) {
            $q->whereNull('academy_unit_id')->orWhere('academy_unit_id', $unitId);
        });
    }
}
