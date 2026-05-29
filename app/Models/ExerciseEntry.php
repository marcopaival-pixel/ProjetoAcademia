<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseEntry extends Model
{
    use Traits\BelongsToCompany;
    use Traits\FillsTenantColumns;
    use Traits\HasClinic;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'entry_date',
        'activity_type',
        'duration_min',
        'calories_burned',
        'sets_data',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'duration_min' => 'integer',
        'calories_burned' => 'integer',
        'sets_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
