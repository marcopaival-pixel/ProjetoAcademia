<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use BelongsToCompany;
    use FillsTenantColumns;
    use HasClinic;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'entity_type',
        'entity_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
