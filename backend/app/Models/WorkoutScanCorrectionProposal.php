<?php

namespace App\Models;

use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutScanCorrectionProposal extends Model
{
    use FillsTenantColumns;
    use HasClinic;

    protected $fillable = [
        'failure_case_id',
        'clinic_id',
        'academy_company_id',
        'correction_type',
        'proposal',
        'confidence',
        'requires_human_review',
        'status',
        'rationale',
    ];

    protected $casts = [
        'proposal' => 'array',
        'confidence' => 'float',
        'requires_human_review' => 'boolean',
    ];

    public function failureCase(): BelongsTo
    {
        return $this->belongsTo(WorkoutScanFailureCase::class, 'failure_case_id');
    }

    public function regressionTests(): HasMany
    {
        return $this->hasMany(WorkoutScanRegressionTest::class, 'correction_proposal_id');
    }
}
