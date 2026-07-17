<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAgentRegistry extends Model
{
    protected $table = 'ai_agent_registry';

    protected $fillable = [
        'agent_key',
        'name',
        'description',
        'allowed_functions',
        'forbidden_functions',
        'provider',
        'model',
        'reasoning_effort',
        'prompt_version',
        'schema_version',
        'requires_audit',
        'auditor_agent_key',
        'fallback_agent_key',
        'active',
    ];

    protected $casts = [
        'allowed_functions' => 'array',
        'forbidden_functions' => 'array',
        'requires_audit' => 'boolean',
        'active' => 'boolean',
    ];
}
