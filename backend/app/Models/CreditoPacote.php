<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditoPacote extends Model
{
    protected $table = 'creditos_pacotes';

    protected $fillable = [
        'nome',
        'quantidade',
        'valor',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'valor' => 'decimal:2',
    ];
}
