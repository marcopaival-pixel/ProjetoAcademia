<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BugIncidentEvent extends Model
{
    public const UPDATED_AT = null;

    public const BUG_RECEIVED = 'BUG_RECEIVED';

    public const AI_ANALYSIS_STARTED = 'AI_ANALYSIS_STARTED';

    public const ROOT_CAUSE_IDENTIFIED = 'ROOT_CAUSE_IDENTIFIED';

    public const PATCH_PROPOSED = 'PATCH_PROPOSED';

    public const PATCH_APPROVED = 'PATCH_APPROVED';

    public const PATCH_APPLIED = 'PATCH_APPLIED';

    public const TESTS_STARTED = 'TESTS_STARTED';

    public const TESTS_APPROVED = 'TESTS_APPROVED';

    public const STAGING_DEPLOYED = 'STAGING_DEPLOYED';

    public const PRODUCTION_APPROVED = 'PRODUCTION_APPROVED';

    public const PRODUCTION_DEPLOYED = 'PRODUCTION_DEPLOYED';

    public const MONITORING_COMPLETED = 'MONITORING_COMPLETED';

    public const BUG_RESOLVED = 'BUG_RESOLVED';

    public const ROLLBACK_EXECUTED = 'ROLLBACK_EXECUTED';

    public const BUG_IGNORED = 'BUG_IGNORED';

    protected $fillable = [
        'bug_incident_id',
        'event_type',
        'payload',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(BugIncident::class, 'bug_incident_id');
    }
}
