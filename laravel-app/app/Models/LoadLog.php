<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'training_plan_exercise_id',
        'exercise_id',
        'log_date',
        'set_number',
        'reps_done',
        'to_failure',
        'weight_kg',
        'rpe',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'weight_kg' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trainingPlanExercise(): BelongsTo
    {
        return $this->belongsTo(TrainingPlanExercise::class);
    }

    public function catalogExercise(): BelongsTo
    {
        return $this->belongsTo(ExerciseCatalog::class, 'exercise_id');
    }
}
