<?php

namespace App\Models;

use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutScanRegressionTest extends Model
{
    use FillsTenantColumns;
    use HasClinic;

    protected $fillable = [
        'correction_proposal_id',
        'failure_case_id',
        'workout_import_log_id',
        'clinic_id',
        'academy_company_id',
        'dataset_type',
        'status',
        'expected_result',
        'actual_result',
        'failure_reason',
    ];

    protected $casts = [
        'expected_result' => 'array',
        'actual_result' => 'array',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(WorkoutScanCorrectionProposal::class, 'correction_proposal_id');
    }
}
