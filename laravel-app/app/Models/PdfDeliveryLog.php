<?php

namespace App\Models;

use App\Enums\PdfDeliveryChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfDeliveryLog extends Model
{
    protected $fillable = [
        'historico_pdf_id',
        'channel',
        'email_destinatario',
        'telefone_destinatario',
        'data_envio',
        'status_envio',
        'tentativas',
        'ultimo_erro',
    ];

    protected function casts(): array
    {
        return [
            'channel' => PdfDeliveryChannel::class,
            'data_envio' => 'datetime',
            'tentativas' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<HistoricoPdf, PdfDeliveryLog>
     */
    public function historicoPdf(): BelongsTo
    {
        return $this->belongsTo(HistoricoPdf::class, 'historico_pdf_id');
    }
}
