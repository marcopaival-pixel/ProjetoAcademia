<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightEntry extends Model
{
    use Traits\BelongsToCompany;
    use Traits\FillsTenantColumns;
    use Traits\HasClinic;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'weighed_at',
        'weight_kg',
    ];

    protected $casts = [
        'weighed_at' => 'date',
        'weight_kg' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
