<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalPlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'max_patients'];

    public function users()
    {
        return $this->hasMany(User::class, 'professional_plan_id');
    }
}
