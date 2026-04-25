<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OmniBusinessHour extends Model
{
    protected $fillable = ['company_id', 'day_of_week', 'open_time', 'close_time', 'is_closed'];
    protected $casts = ['is_closed' => 'boolean'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(OmniCompany::class, 'company_id');
    }
}
