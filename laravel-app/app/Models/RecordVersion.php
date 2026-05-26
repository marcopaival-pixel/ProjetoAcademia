<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'entity_type',
        'entity_id',
        'version_number',
        'data',
        'notes',
    ];

    protected $casts = [
        'data' => 'array',
        'version_number' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
