<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalAppointment extends Model
{
    use Traits\FiltersByProfessional;

    protected $fillable = [
        'professional_id',
        'patient_id',
        'appointment_at',
        'status', // scheduled, confirmed, in_progress, finished, cancelled, no_show
        'service_type',
        'notes',
    ];

    protected $casts = [
        'appointment_at' => 'datetime',
    ];

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Agendado',
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_IN_PROGRESS => 'Em atendimento',
            self::STATUS_FINISHED => 'Finalizado',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_NO_SHOW => 'Faltou',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
