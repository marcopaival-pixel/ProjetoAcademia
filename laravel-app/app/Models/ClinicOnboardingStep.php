<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicOnboardingStep extends Model
{
    protected $fillable = [
        'academy_company_id',
        'step_key',
        'is_completed',
        'completed_at',
        'data',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'data' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }
}
