<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingPlan extends Model
{
    use HasFactory, Traits\FiltersByProfessional;

    protected $fillable = [
        'user_id',
        'professional_id',
        'creator_id',
        'name',
        'plan_label',
        'description',
        'goal',
        'frequency',
        'difficulty',
        'estimated_duration',
        'is_active',
        'student_profile',
        'split_type',
        'status',
        'days_of_week',
        'is_template',
        'total_volume',
        'muscles_worked',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'muscles_worked' => 'array',
        'is_active' => 'boolean',
        'is_template' => 'boolean',
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
