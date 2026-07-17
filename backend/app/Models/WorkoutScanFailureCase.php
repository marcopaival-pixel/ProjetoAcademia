<?php

namespace App\Models;

use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutScanFailureCase extends Model
{
    use FillsTenantColumns;
    use HasClinic;

    protected $fillable = [
        'workout_import_log_id',
        'user_id',
        'clinic_id',
        'academy_company_id',
        'image_path',
        'error_type',
        'failed_stage',
        'ai_identified_content',
        'expected_result',
        'model_version',
        'error_message',
        'status',
    ];

    protected $casts = [
        'ai_identified_content' => 'array',
        'expected_result' => 'array',
    ];

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(WorkoutImportLog::class, 'workout_import_log_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(WorkoutScanCorrectionProposal::class, 'failure_case_id');
    }
}
