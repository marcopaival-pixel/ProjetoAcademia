<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvolutionPhoto extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'photo_path',
        'type',
        'registered_date',
        'weight_kg',
        'notes'
    ];
}
