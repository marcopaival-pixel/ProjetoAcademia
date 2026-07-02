<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodLog extends Model
{
    use HasFactory;
    use Traits\BelongsToScopedParent;

    protected static function scopedParentRelationName(): string
    {
        return 'user';
    }

    protected $fillable = [
        'user_id',
        'professional_id',
        'mood_score',
        'energy_level',
        'sleep_hours',
        'stress_level',
        'notes',
        'is_confidential',
        'logged_at',
    ];

    protected $casts = [
        'mood_score'      => 'integer',
        'energy_level'    => 'integer',
        'sleep_hours'     => 'float',
        'stress_level'    => 'integer',
        'is_confidential' => 'boolean',
        'logged_at'       => 'date',
    ];

    /**
     * Retorna apenas registros que podem ser exibidos ao paciente
     * (registros do próprio paciente ou liberados pelo profissional).
     */
    public function scopeVisibleToPatient($query)
    {
        return $query->where('is_confidential', false);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
