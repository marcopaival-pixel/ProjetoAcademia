<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class ConfiguracaoEmail extends Model
{
    protected $table = 'configuracao_email';

    protected $fillable = [
        'empresa_id',
        'nome_provedor',
        'tipo_envio',
        'preset',
        'smtp_host',
        'smtp_porta',
        'smtp_usuario',
        'smtp_senha',
        'criptografia',
        'email_remetente',
        'nome_remetente',
        'timeout',
        'limite_envio_por_hora',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'smtp_porta' => 'integer',
            'timeout' => 'integer',
            'limite_envio_por_hora' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<AcademyCompany, ConfiguracaoEmail>
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'empresa_id');
    }

    public function setSmtpSenhaAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }
        $this->attributes['smtp_senha'] = Crypt::encryptString($value);
    }

    public function getDecryptedPassword(): ?string
    {
        $raw = $this->attributes['smtp_senha'] ?? null;
        if ($raw === null || $raw === '') {
            return null;
        }
        try {
            return Crypt::decryptString($raw);
        } catch (\Throwable) {
            return is_string($raw) ? $raw : null;
        }
    }

    /**
     * Resolve host/porta/criptografia para envio SMTP (inclui tipo API mapeado para SMTP).
     *
     * @return array{
     *   host: string|null,
     *   port: int,
     *   encryption: string|null,
     *   username: string|null,
     *   password: string|null,
     *   from_address: string|null,
     *   from_name: string|null,
     *   timeout: int
     * }
     */
    public function resolveMailSettings(): array
    {
        $host = $this->smtp_host;
        $port = (int) $this->smtp_porta;
        $encryption = $this->criptografia === 'none' || $this->criptografia === '' ? null : $this->criptografia;
        $username = $this->smtp_usuario;
        $password = $this->getDecryptedPassword();

        if ($this->preset && $this->preset !== 'custom') {
            $defaults = \App\Support\EmailProviderPreset::smtpDefaults($this->preset);
            if ($defaults !== null) {
                $host = $host ?: $defaults['host'];
                $port = $port ?: (int) $defaults['porta'];
                $encryption = $encryption ?? ($defaults['criptografia'] === 'none' ? null : $defaults['criptografia']);
                if (($this->tipo_envio === 'api' || $this->preset === 'sendgrid') && $username === null) {
                    $username = $defaults['smtp_usuario_hint'] ?? $username;
                }
            }
        }

        if ($this->tipo_envio === 'api' && $this->preset === 'sendgrid') {
            $host = $host ?: 'smtp.sendgrid.net';
            $port = $port ?: 587;
            $username = $username ?: 'apikey';
        }

        return [
            'host' => $host,
            'port' => $port ?: 587,
            'encryption' => $encryption,
            'username' => $username,
            'password' => $password,
            'from_address' => $this->email_remetente,
            'from_name' => $this->nome_remetente ?: config('app.name'),
            'timeout' => max(5, (int) $this->timeout),
        ];
    }
}
