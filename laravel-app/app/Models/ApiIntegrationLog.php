<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiIntegrationLog extends Model
{
    protected $fillable = [
        'api_name',
        'endpoint',
        'status_code',
        'response_time_ms',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}
