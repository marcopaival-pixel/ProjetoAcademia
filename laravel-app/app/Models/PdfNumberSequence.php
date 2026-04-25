<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfNumberSequence extends Model
{
    protected $table = 'pdf_number_sequences';

    protected $fillable = [
        'academy_company_id',
        'tipo_documento',
        'ano',
        'sequencia_atual',
    ];

    protected function casts(): array
    {
        return [
            'ano' => 'integer',
            'sequencia_atual' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<AcademyCompany, PdfNumberSequence>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }
}
