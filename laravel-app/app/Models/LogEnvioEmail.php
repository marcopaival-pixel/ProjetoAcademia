<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogEnvioEmail extends Model
{
    protected $table = 'log_envio_email';

    public const STATUS_ENVIADO = 'enviado';

    public const STATUS_FALHA = 'falha';

    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'tipo_envio',
        'email_destino',
        'assunto',
        'mensagem',
        'status',
        'erro',
        'ip',
        'data_envio',
    ];

    protected function casts(): array
    {
        return [
            'data_envio' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<AcademyCompany, LogEnvioEmail>
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'empresa_id');
    }

    /**
     * @return BelongsTo<User, LogEnvioEmail>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
