<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OmniMessage extends Model
{
    use Traits\FiltersByProfessional;

    protected $fillable = [
        'conversation_id', 'sender_type', 'sender_id', 
        'content', 'content_type', 'file_path', 'read_at'
    ];

    protected $casts = ['read_at' => 'datetime'];

    public function conversation(): BelongsTo { return $this->belongsTo(OmniConversation::class, 'conversation_id'); }
}
