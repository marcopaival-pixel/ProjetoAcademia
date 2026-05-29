<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingPlanExercise extends Model
{
    use HasFactory;
    use Traits\BelongsToScopedParent;

    protected static function scopedParentRelationName(): string
    {
        return 'trainingPlan';
    }

    protected static function scopedParentTenantColumnName(): string
    {
        return 'clinic_id';
    }

    protected $fillable = [
        'training_plan_id',
        'exercise_id',
        'custom_name',
        'position',
        'notes',
    ];

    public function trainingPlan(): BelongsTo
    {
        return $this->belongsTo(TrainingPlan::class);
    }

    public function catalogExercise(): BelongsTo
    {
        return $this->belongsTo(ExerciseCatalog::class, 'exercise_id');
    }

    public function sets(): HasMany
    {
        return $this->hasMany(ExerciseSet::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LoadLog::class);
    }
}
