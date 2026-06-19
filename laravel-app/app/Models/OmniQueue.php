<?php

namespace App\Models;

use App\Models\Traits\BelongsToOmniCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OmniQueue extends Model
{
    use BelongsToOmniCompany;
    protected $fillable = ['company_id', 'name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function company(): BelongsTo 
    { 
        return $this->belongsTo(OmniCompany::class, 'company_id'); 
    }
}
