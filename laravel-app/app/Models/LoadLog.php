<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoadLog extends Model
{
    use HasFactory;
    use Traits\BelongsToCompany;
    use Traits\FillsTenantColumns;
    use Traits\HasClinic;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'training_plan_exercise_id',
        'exercise_id',
        'log_date',
        'set_number',
        'reps_done',
        'to_failure',
        'weight_kg',
        'one_rm',
        'rpe',
        'notes',
    ];

    protected static function booted()
    {
        static::saving(function ($log) {
            if ($log->weight_kg && $log->reps_done) {
                // Fórmula de Brzycki: Weight / (1.0278 - 0.0278 * Reps)
                $denominator = 1.0278 - (0.0278 * $log->reps_done);
                $log->one_rm = $denominator > 0 ? $log->weight_kg / $denominator : $log->weight_kg;
            }
        });
    }


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
