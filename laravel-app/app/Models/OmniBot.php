<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmniBot extends Model
{
    protected $fillable = ['company_id', 'name', 'whatsapp_phone', 'is_active', 'business_hours', 'out_of_office_message'];
    protected $casts = ['business_hours' => 'json'];

    public function steps() { return $this->hasMany(OmniBotStep::class, 'bot_id'); }
}
