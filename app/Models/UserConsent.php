<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    use HasFactory;
    use Traits\BelongsToUserCompany;

    protected $table = 'user_consents';
    public $timestamps = false; // Solo created_at

    protected $fillable = [
        'user_id',
        'version',
        'consent_type',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
