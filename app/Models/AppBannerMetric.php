<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppBannerMetric extends Model
{
    use HasFactory;
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'event_type',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
