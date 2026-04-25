<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternalEmail extends Model
{
    use HasFactory;

    protected $table = 'internal_emails';

    protected $appends = ['lida'];

    public function getLidaAttribute()
    {
        return $this->attributes['is_read'] ?? null;
    }

    public function setLidaAttribute($value)
    {
        $this->attributes['is_read'] = $value;
    }

    
    // Enabling timestamps since we added them in the migration
    public $timestamps = true;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'subject',
        'content',
        'is_read',
        'sent_at',
        'read_at',
        'excluded_at_sender',
        'excluded_at_receiver',
        'status',
        'parent_id',
        'is_system',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'excluded_at_sender' => 'datetime',
        'excluded_at_receiver' => 'datetime',
        'is_system' => 'boolean',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(InternalEmail::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(InternalEmail::class, 'parent_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(InternalEmailAnexo::class, 'email_id');
    }

    // Custom scopes for the "Folders"
    public function scopeInbox($query, $userId)
    {
        return $query->where('recipient_id', $userId)
                     ->where('status', 'sent')
                     ->whereNull('excluded_at_receiver');
    }

    public function scopeSent($query, $userId)
    {
        return $query->where('sender_id', $userId)
                     ->where('status', 'sent')
                     ->whereNull('excluded_at_sender');
    }

    public function scopeTrash($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('recipient_id', $userId)->whereNotNull('excluded_at_receiver')
              ->orWhere('sender_id', $userId)->whereNotNull('excluded_at_sender');
        });
    }

    public function scopeOutbox($query, $userId)
    {
        return $query->where('sender_id', $userId)
                     ->whereIn('status', ['draft', 'outbox']);
    }
}
