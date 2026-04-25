<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadInteraction extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'tipo_contato',
        'descricao',
        'data_contato',
    ];

    protected $casts = [
        'data_contato' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
