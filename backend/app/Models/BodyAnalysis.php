<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyAnalysis extends Model
{
    use HasFactory;
    use Traits\FillsTenantColumns;
    use Traits\HasClinic;

    protected $table = 'body_analyses';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'photo_path',
        'view_type',
        'landmarks',
        'metrics',
        'ai_summary',
        'analysis_version',
        'vision_model',
        'vision_confidence',
        'vision_raw_payload',
        'shared_options',
    ];

    protected $casts = [
        'landmarks' => 'array',
        'metrics' => 'array',
        'ai_summary' => 'array',
        'vision_confidence' => 'float',
        'vision_raw_payload' => 'array',
        'shared_options' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
