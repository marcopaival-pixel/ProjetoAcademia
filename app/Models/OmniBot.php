<?php

namespace App\Models;

use App\Models\Traits\BelongsToOmniCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OmniBot extends Model
{
    use BelongsToOmniCompany;
    protected $fillable = ['company_id', 'name', 'whatsapp_phone', 'is_active', 'business_hours', 'out_of_office_message'];
    protected $casts = ['business_hours' => 'json'];

    public function steps(): HasMany
    {
        return $this->hasMany(OmniBotStep::class, 'bot_id');
    }
}
