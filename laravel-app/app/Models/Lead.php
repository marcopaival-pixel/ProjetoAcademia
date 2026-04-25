<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'empresa',
        'origem',
        'responsavel_id',
        'status',
        'observacao',
        'valor_estimado',
        'previsao_fechamento',
        'converted_user_id',
    ];

    protected $casts = [
        'previsao_fechamento' => 'datetime',
    ];

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function interactions()
    {
        return $this->hasMany(LeadInteraction::class);
    }

    public function proposals()
    {
        return $this->hasMany(CommercialProposal::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function onboardingSteps()
    {
        return $this->hasMany(OnboardingStep::class)->orderBy('order');
    }
}
