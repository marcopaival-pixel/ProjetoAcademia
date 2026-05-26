<?php

namespace App\Models;

use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutImportLog extends Model
{
    use FillsTenantColumns;
    use HasClinic;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'image_path',
        'raw_ocr_text',
        'structured_json',
        'status',
        'error_message',
        'ai_confidence',
    ];

    protected $casts = [
        'structured_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
