<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthMetric extends Model
{
    use HasFactory;

    /**
     * Constantes para tipos de métricas padronizadas
     */
    const TYPE_HRV = 'hrv'; // Variabilidade da Frequência Cardíaca (ms)
    const TYPE_SLEEP_HOURS = 'sleep_hours'; // Horas de sono
    const TYPE_SLEEP_QUALITY = 'sleep_quality'; // Qualidade (0-100%)
    const TYPE_RECOVERY = 'recovery_score'; // Pontuação de recuperação (0-100%)
    const TYPE_RESTING_HR = 'resting_hr'; // Frequência Cardíaca em Repouso (bpm)
    const TYPE_SPO2 = 'spo2'; // Oxigenação sanguínea (%)

    protected $fillable = [
        'user_id',
        'type',
        'value',
        'unit',
        'source',
        'recorded_at',
        'metadata'
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'recorded_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relacionamento com o Usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Escopo para filtrar por tipo
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Escopo para buscar registros recentes
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('recorded_at', 'desc');
    }
}
