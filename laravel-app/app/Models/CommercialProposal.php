<?php

namespace App\Models;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CommercialProposal extends Model
{
    protected $fillable = [
        'lead_id',
        'plan_id',
        'representative_id',
        'valor',
        'desconto',
        'validade',
        'status',
        'token',
        'observacoes',
        'clinic_name',
        'clinic_cnpj',
        'clinic_city',
        'clinic_state',
        'clinic_phone',
        'clinic_contact',
        'clinic_id',
    ];

    protected $casts = [
        'validade' => 'date',
        'valor' => 'decimal:2',
        'desconto' => 'decimal:2',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function getValorFinalAttribute()
    {
        return $this->valor - $this->desconto;
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representative_id');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function referralCode(): HasOne
    {
        return $this->hasOne(ReferralCode::class, 'commercial_proposal_id');
    }

    public function generateQrCode()
    {
        $representative = $this->getRelationValue('representative');
        if (! $representative) {
            return null;
        }

        $representativeProfile = $representative->getRelationValue('representativeProfile');
        if (! $representativeProfile) {
            return null;
        }

        $referralCode = $this->getRelationValue('referralCode');
        $codeStr = $referralCode ? $referralCode->getAttribute('code') : $representativeProfile->getAttribute('code');

        $url = route('plano', [
            'ref' => $codeStr,
            'plan_id' => $this->plan_id,
            'discount' => $this->desconto,
        ]);

        $qrCode = Builder::create()
            ->writer(new PngWriter)
            ->writerOptions([])
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(200)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return $qrCode->getDataUri();
    }
}
