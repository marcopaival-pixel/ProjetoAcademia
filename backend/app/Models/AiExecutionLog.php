<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiExecutionLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'evolution_report_id',
        'agent',
        'provider',
        'model',
        'reasoning_effort',
        'prompt_version',
        'schema_version',
        'request_hash',
        'response_id',
        'input_tokens',
        'output_tokens',
        'cost',
        'duration_ms',
        'attempt',
        'http_status',
        'status',
        'error_code',
        'error',
        'created_at',
    ];

    protected $casts = [
        'cost' => 'decimal:6',
        'created_at' => 'datetime',
    ];

    public function evolutionReport()
    {
        return $this->belongsTo(EvolutionReport::class);
    }
}
