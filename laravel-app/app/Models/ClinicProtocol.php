<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicProtocol extends Model
{
    protected $fillable = [
        'academy_company_id',
        'especialidade_id',
        'type',
        'name',
        'description',
        'objective',
        'protocol',
        'frequency',
        'duration',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(AcademyCompany::class, 'academy_company_id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class, 'especialidade_id');
    }
}
