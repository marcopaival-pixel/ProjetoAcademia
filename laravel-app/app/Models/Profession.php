<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $fillable = ['name', 'slug'];

    public function specialties()
    {
        return $this->hasMany(Especialidade::class);
    }
}
