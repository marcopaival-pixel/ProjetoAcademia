<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'lead_id',
        'proposal_id',
        'status',
        'signed_at',
        'content',
        'token',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function proposal()
    {
        return $this->belongsTo(CommercialProposal::class);
    }
}
