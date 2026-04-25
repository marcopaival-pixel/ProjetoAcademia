<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfSignatureAuditLog extends Model
{
    protected $fillable = [
        'historico_pdf_id',
        'user_id',
        'evento',
        'detalhe',
        'ip_address',
    ];

    /**
     * @return BelongsTo<HistoricoPdf, PdfSignatureAuditLog>
     */
    public function historicoPdf(): BelongsTo
    {
        return $this->belongsTo(HistoricoPdf::class, 'historico_pdf_id');
    }

    /**
     * @return BelongsTo<User, PdfSignatureAuditLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
