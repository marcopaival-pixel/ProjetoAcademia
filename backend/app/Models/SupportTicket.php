<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'subject',
        'priority',
        'status',
        'category',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class)->oldest();
    }
}
