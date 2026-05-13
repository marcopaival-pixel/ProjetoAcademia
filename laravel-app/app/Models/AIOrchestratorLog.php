<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\HasClinic;

class AIOrchestratorLog extends Model
{
    use HasClinic;
    protected $fillable = [
        'user_id',
        'clinic_id',
        'agent_name',
        'model_name',
        'user_message',
        'ai_response',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'cost_usd',
        'execution_time_ms',
        'status',
        'context',
        'error_message'
    ];

    protected $casts = [
        'context' => 'array',
        'cost_usd' => 'float',
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'total_tokens' => 'integer',
        'execution_time_ms' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
