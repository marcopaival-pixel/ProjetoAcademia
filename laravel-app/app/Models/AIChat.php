<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;

class AIChat extends Model
{
    use FillsTenantColumns, HasClinic;
    protected $table = 'ai_chats';

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'role',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
