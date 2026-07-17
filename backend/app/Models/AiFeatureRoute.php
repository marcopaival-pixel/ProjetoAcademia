<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiFeatureRoute extends Model
{
    protected $table = 'ai_feature_routes';

    protected $fillable = [
        'feature_key',
        'entry_agent_key',
        'orchestrator',
        'requires_validation',
        'requires_audit',
        'auditor_agent_key',
        'failure_behavior',
        'active',
    ];

    protected $casts = [
        'requires_validation' => 'boolean',
        'requires_audit' => 'boolean',
        'active' => 'boolean',
    ];
}
