<?php

namespace App\Models;

use App\Models\Traits\FiltersByProfessionalOwner;
use Illuminate\Database\Eloquent\Model;

class ProfessionalFinanceGoal extends Model
{
    use FiltersByProfessionalOwner;
    protected $fillable = [
        'professional_id',
        'monthly_goal',
    ];

    protected $casts = [
        'monthly_goal' => 'decimal:2',
    ];

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
