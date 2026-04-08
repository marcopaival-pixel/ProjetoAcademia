<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'plan_label',
        'description',
        'goal',
        'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(TrainingPlanExercise::class)->orderBy('position');
    }

    public function targetAreas(): HasMany
    {
        return $this->hasMany(WorkoutTargetArea::class);
    }
}
