<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyUnit extends Model
{
    use BelongsToCompany;
    protected $fillable = [
        'academy_company_id',
        'name',
        'code',
        'settings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<AcademyCompany, AcademyUnit>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }

    /**
     * @return HasMany<PdfTemplate, AcademyUnit>
     */
    public function pdfTemplates(): HasMany
    {
        return $this->hasMany(PdfTemplate::class);
    }
}
