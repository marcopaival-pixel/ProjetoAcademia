<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmniBot extends Model
{
    protected $fillable = ['company_id', 'name', 'whatsapp_phone', 'is_active', 'business_hours', 'out_of_office_message'];
    protected $casts = ['business_hours' => 'json'];

    public function steps() { return $this->hasMany(OmniBotStep::class, 'bot_id'); }
}

class OmniBotStep extends Model
{
    protected $fillable = ['bot_id', 'label', 'type', 'content', 'is_start', 'next_step_id'];

    public function options() { return $this->hasMany(OmniBotOption::class, 'step_id'); }
}

class OmniBotOption extends Model
{
    protected $fillable = ['step_id', 'trigger_value', 'label', 'destination_step_id'];
}
