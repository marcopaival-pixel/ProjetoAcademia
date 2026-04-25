<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfGenerationLog extends Model
{
    public const ACTION_PREVIEW = 'preview';

    public const ACTION_DOWNLOAD = 'download';

    protected $fillable = [
        'user_id',
        'pdf_template_id',
        'historico_pdf_id',
        'document_type',
        'template_name',
        'action',
        'filename',
        'status',
        'error_message',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return BelongsTo<User, PdfGenerationLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<PdfTemplate, PdfGenerationLog>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(PdfTemplate::class, 'pdf_template_id');
    }

    /**
     * @return BelongsTo<HistoricoPdf, PdfGenerationLog>
     */
    public function historicoPdf(): BelongsTo
    {
        return $this->belongsTo(HistoricoPdf::class, 'historico_pdf_id');
    }
}
