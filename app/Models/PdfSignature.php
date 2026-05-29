<?php

namespace App\Models;

use App\Enums\PdfSignatureMode;
use App\Enums\PdfSignatureRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfSignature extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'historico_pdf_id',
        'user_id',
        'signer_name',
        'tipo_assinatura',
        'modo',
        'imagem_assinatura',
        'ip_address',
        'data_assinatura',
    ];

    protected function casts(): array
    {
        return [
            'tipo_assinatura' => PdfSignatureRole::class,
            'modo' => PdfSignatureMode::class,
            'data_assinatura' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<HistoricoPdf, PdfSignature>
     */
    public function historicoPdf(): BelongsTo
    {
        return $this->belongsTo(HistoricoPdf::class, 'historico_pdf_id');
    }

    /**
     * @return BelongsTo<User, PdfSignature>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
