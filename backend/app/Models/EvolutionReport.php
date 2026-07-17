<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvolutionReport extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_COMPLETED_WITH_LIMITATIONS = 'completed_with_limitations';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'consent_id',
        'current_session_date',
        'previous_session_date',
        'status',
        'provider',
        'validation_result',
        'objective_metrics',
        'comparison_result',
        'audit_result',
        'final_report',
        'confidence',
        'failure_reason',
        'limited_reason',
        'prompt_version',
        'schema_version',
        'started_at',
        'completed_at',
        'published_at',
    ];

    protected $casts = [
        'current_session_date' => 'date',
        'previous_session_date' => 'date',
        'validation_result' => 'array',
        'objective_metrics' => 'array',
        'comparison_result' => 'array',
        'audit_result' => 'array',
        'final_report' => 'array',
        'confidence' => 'decimal:4',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consent()
    {
        return $this->belongsTo(UserConsent::class, 'consent_id');
    }

    public function executionLogs()
    {
        return $this->hasMany(AiExecutionLog::class);
    }
}
