<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OmniCompany extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'is_active', 'settings'];
    protected $casts = ['settings' => 'array', 'is_active' => 'boolean'];

    public function channels(): HasMany { return $this->hasMany(OmniChannel::class, 'company_id'); }
    public function agents(): HasMany { return $this->hasMany(OmniAgent::class, 'company_id'); }
    public function queues(): HasMany { return $this->hasMany(OmniQueue::class, 'company_id'); }
    public function chatbotRules(): HasMany { return $this->hasMany(OmniChatbotRule::class, 'company_id'); }
    public function businessHours(): HasMany { return $this->hasMany(OmniBusinessHour::class, 'company_id'); }
}
