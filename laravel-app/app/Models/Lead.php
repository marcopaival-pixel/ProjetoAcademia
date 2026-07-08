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
        'converted_company_id',
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

    public function convertedCompany()
    {
        return $this->belongsTo(AcademyCompany::class, 'converted_company_id');
    }

    public function getAiProbabilityAttribute()
    {
        return app(\App\Services\AiCommercialAssistantService::class)->estimateProbability($this);
    }

    public function getAiNextActionAttribute()
    {
        return app(\App\Services\AiCommercialAssistantService::class)->suggestNextAction($this);
    }

    public function getAiSummaryAttribute()
    {
        return app(\App\Services\AiCommercialAssistantService::class)->summarizeInteractions($this);
    }
}
