<?php

namespace App\Models;

use App\Models\Traits\BelongsToOmniCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OmniConversation extends Model
{
    use BelongsToOmniCompany;
    protected $fillable = [
        'company_id', 'channel_id', 'customer_external_id', 
        'customer_name', 'agent_id', 'queue_id', 'status', 'last_message_at'
    ];

    protected $casts = ['last_message_at' => 'datetime'];

    public function company(): BelongsTo { return $this->belongsTo(OmniCompany::class, 'company_id'); }
    public function channel(): BelongsTo { return $this->belongsTo(OmniChannel::class, 'channel_id'); }
    public function agent(): BelongsTo { return $this->belongsTo(OmniAgent::class, 'agent_id'); }
    public function queue(): BelongsTo { return $this->belongsTo(OmniQueue::class, 'queue_id'); }
    public function messages(): HasMany { return $this->hasMany(OmniMessage::class, 'conversation_id'); }
}
