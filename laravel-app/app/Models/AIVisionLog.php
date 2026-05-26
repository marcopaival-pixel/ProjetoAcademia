<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;

class AIVisionLog extends Model
{
    use FillsTenantColumns, HasClinic;

    protected $table = 'ai_vision_logs';

    protected $fillable = [
        'orchestrator_log_id',
        'user_id',
        'clinic_id',
        'academy_company_id',
        'document_type',
        'confidence',
        'image_path',
        'image_hash',
        'extracted_data',
        'warnings',
        'model_name',
        'total_tokens',
        'cost_usd',
        'execution_time_ms'
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'warnings' => 'array',
        'confidence' => 'float',
        'cost_usd' => 'decimal:6',
        'total_tokens' => 'integer',
        'execution_time_ms' => 'integer',
    ];

    public function orchestratorLog()
    {
        return $this->belongsTo(AIOrchestratorLog::class, 'orchestrator_log_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
