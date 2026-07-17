<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommercialProposal extends Model
{
    protected $fillable = [
        'lead_id',
        'plan_id',
        'valor',
        'desconto',
        'validade',
        'status',
        'token',
        'observacoes',
    ];

    protected $casts = [
        'validade' => 'date',
        'valor' => 'decimal:2',
        'desconto' => 'decimal:2',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function getValorFinalAttribute()
    {
        return $this->valor - $this->desconto;
    }
}
