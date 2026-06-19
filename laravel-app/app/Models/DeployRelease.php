<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeployRelease extends Model
{
    public const ENV_HOMOLOG = 'homologacao';

    public const ENV_PRODUCTION = 'production';

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    public const HOMOLOG_PENDING = 'pending';

    public const HOMOLOG_APPROVED = 'approved';

    public const HOMOLOG_REJECTED = 'rejected';

    protected $fillable = [
        'version',
        'environment',
        'status',
        'homolog_status',
        'impact_level',
        'risk_level',
        'deployed_by',
        'git_branch',
        'git_commit',
        'notes',
        'failure_message',
        'files_changed_count',
        'deployed_at',
        'finished_at',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'deployed_at' => 'datetime',
            'finished_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public function deployer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deployed_by');
    }

    public function environmentLabel(): string
    {
        return match ($this->environment) {
            self::ENV_HOMOLOG => 'Homologação',
            self::ENV_PRODUCTION => 'Produção',
            default => $this->environment,
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_IN_PROGRESS => 'Em andamento',
            self::STATUS_SUCCESS => 'Sucesso',
            self::STATUS_FAILED => 'Falhou',
            default => $this->status,
        };
    }
}
