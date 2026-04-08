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
    
    // Enabling timestamps since we added them in the migration
    public $timestamps = true;

    protected $fillable = [
        'remetente_id',
        'destinatario_id',
        'assunto',
        'mensagem',
        'lida',
        'data_envio',
        'data_leitura',
        'excluded_at_sender',
        'excluded_at_receiver',
        'status',
        'parent_id',
        'is_system',
    ];

    protected $casts = [
        'lida' => 'boolean',
        'data_envio' => 'datetime',
        'data_leitura' => 'datetime',
        'excluded_at_sender' => 'datetime',
        'excluded_at_receiver' => 'datetime',
        'is_system' => 'boolean',
    ];

    public function remetente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(InternalEmail::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(InternalEmail::class, 'parent_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(InternalEmailAnexo::class, 'mensagem_id');
    }

    // Custom scopes for the "Folders"
    public function scopeInbox($query, $userId)
    {
        return $query->where('destinatario_id', $userId)
                     ->where('status', 'sent')
                     ->whereNull('excluded_at_receiver');
    }

    public function scopeSent($query, $userId)
    {
        return $query->where('remetente_id', $userId)
                     ->where('status', 'sent')
                     ->whereNull('excluded_at_sender');
    }

    public function scopeTrash($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('destinatario_id', $userId)->whereNotNull('excluded_at_receiver')
              ->orWhere('remetente_id', $userId)->whereNotNull('excluded_at_sender');
        });
    }

    public function scopeOutbox($query, $userId)
    {
        return $query->where('remetente_id', $userId)
                     ->whereIn('status', ['draft', 'outbox']);
    }
}
