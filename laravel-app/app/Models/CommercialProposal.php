<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function representative()
    {
        return $this->belongsTo(User::class, 'representative_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function referralCode()
    {
        return $this->hasOne(ReferralCode::class, 'commercial_proposal_id');
    }

    public function generateQrCode()
    {
        if (!$this->representative || !$this->representative->representativeProfile) {
            return null;
        }

        $codeStr = $this->referralCode ? $this->referralCode->code : $this->representative->representativeProfile->code;

        $url = route('plano', [
            'ref' => $codeStr,
            'plan_id' => $this->plan_id,
            'discount' => $this->desconto
        ]);

        $qrCode = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->writerOptions([])
            ->data($url)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->errorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh::class)
            ->size(200)
            ->margin(10)
            ->roundBlockSizeMode(\Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin::class)
            ->build();

        return $qrCode->getDataUri();
    }
}
