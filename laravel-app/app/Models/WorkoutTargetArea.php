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
        'muscle_id',
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

    public function muscle()
    {
        return $this->belongsTo(Muscle::class);
    }
}
