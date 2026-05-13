<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'badge_code',
        'title',
        'description',
        'icon_url',
        'unlocked_at'
    ];

    protected $casts = [
        'unlocked_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
