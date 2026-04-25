<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalBranding extends Model
{
    protected $fillable = [
        'user_id',
        'clinic_name',
        'primary_color',
        'accent_color',
        'logo_path',
        'custom_domain',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
