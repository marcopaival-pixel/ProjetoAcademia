<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OmniAgent extends Model
{
    protected $fillable = ['user_id', 'company_id', 'status', 'max_simultaneous_chats'];
    
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    public function company(): BelongsTo 
    { 
        return $this->belongsTo(OmniCompany::class, 'company_id'); 
    }
}
