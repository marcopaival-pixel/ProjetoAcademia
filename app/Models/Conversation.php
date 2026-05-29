<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use Traits\FiltersByProfessional;

    protected $fillable = ['user_one_id', 'user_two_id', 'tipo', 'status'];

    public const STATUS_ABERTO = 'ABERTO';
    public const STATUS_EM_ANDAMENTO = 'EM_ANDAMENTO';
    public const STATUS_RESOLVIDO = 'RESOLVIDO';

    public const TIPO_SUPORTE = 'SUPORTE';
    public const TIPO_FINANCEIRO = 'FINANCEIRO';

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the other user in the conversation.
     */
    public function getOtherUser(int $currentUserId): ?User
    {
        if ($this->user_one_id === $currentUserId) {
            return $this->userTwo;
        }
        return $this->userOne;
    }
}
