<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutTargetArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'training_plan_id',
        'target_area',
        'reference_photo_path'
    ];

    public function trainingPlan()
    {
        return $this->belongsTo(TrainingPlan::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
