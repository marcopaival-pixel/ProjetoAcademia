<?php

namespace App\Models;

use App\Models\Traits\HasClinic;
use Illuminate\Database\Eloquent\Model;

class AdminClinicAccessLog extends Model
{
    use HasClinic;
    protected $fillable = [
        'admin_user_id',
        'clinic_id',
        'motivo_acesso',
        'descricao',
        'data_hora_entrada',
        'data_hora_saida',
        'ip',
        'duracao_acesso',
    ];

    protected $casts = [
        'data_hora_entrada' => 'datetime',
        'data_hora_saida' => 'datetime',
    ];

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}
