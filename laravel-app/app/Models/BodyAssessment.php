<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyAssessment extends Model
{
    use HasFactory, Traits\FiltersByProfessional;

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
        'status',
        'created_by',
        'professional_id',
        'blood_pressure',
        'heart_rate',
        'ai_suggestions',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'ai_suggestions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
