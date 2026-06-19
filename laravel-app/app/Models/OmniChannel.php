<?php

namespace App\Models;

use App\Models\Traits\BelongsToOmniCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OmniChannel extends Model
{
    use BelongsToOmniCompany;
    protected $fillable = ['company_id', 'type', 'name', 'is_active', 'config'];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(OmniCompany::class, 'company_id');
    }
}
