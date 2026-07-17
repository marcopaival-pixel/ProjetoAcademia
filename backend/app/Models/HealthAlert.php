<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthAlert extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'type',
        'severity',
        'message',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
