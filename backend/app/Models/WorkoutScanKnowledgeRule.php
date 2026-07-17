<?php

namespace App\Models;

use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutScanKnowledgeRule extends Model
{
    use FillsTenantColumns;
    use HasClinic;

    protected $fillable = [
        'correction_proposal_id',
        'clinic_id',
        'academy_company_id',
        'rule_key',
        'version',
        'correction_type',
        'rule_payload',
        'error_cause',
        'status',
        'applied_at',
        'reverted_at',
        'history',
    ];

    protected $casts = [
        'rule_payload' => 'array',
        'history' => 'array',
        'applied_at' => 'datetime',
        'reverted_at' => 'datetime',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(WorkoutScanCorrectionProposal::class, 'correction_proposal_id');
    }
}
