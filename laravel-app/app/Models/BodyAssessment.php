<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weight_kg',
        'bf_percent',
        'muscle_percent',
        'neck',
        'chest',
        'waist',
        'abdomen',
        'hips',
        'bicep_l',
        'bicep_r',
        'forearm_l',
        'forearm_r',
        'thigh_l',
        'thigh_r',
        'calf_l',
        'calf_r',
        'assessment_date',
        'notes',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
