<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_plan_exercise_id',
        'set_number',
        'reps_target',
        'weight_target',
        'rest_seconds',
    ];

    public function trainingPlanExercise(): BelongsTo
    {
        return $this->belongsTo(TrainingPlanExercise::class);
    }
}
