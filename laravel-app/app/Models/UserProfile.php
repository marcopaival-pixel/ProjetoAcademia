<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use Traits\BelongsToUserCompany;

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';

    const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'birth_date',
        'sex',
        'height_cm',
        'activity_level',
        'climate',
        'goal',
        'daily_calorie_target',
        'protein_target_g',
        'carbs_target_g',
        'fat_target_g',
        'water_target_ml',
        'is_water_target_auto',
        'target_weight_kg',
        'training_days_per_week',
        'address',
        'city',
        'state',
        'has_disease',
        'disease_details',
        'has_injury',
        'injury_details',
        'uses_medication',
        'medication_details',
        'has_allergy',
        'allergy_details',
        'emergency_contact_name',
        'emergency_contact_phone',
        'profile_completed_at',
        'physical_level',
        'experience_level',
        'training_location',
        'cardio_frequency',
        'sleep_hours',
        'nutrition_quality',
        'available_daily_time_mins',
        'fitness_notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'height_cm' => 'integer',
            'daily_calorie_target' => 'integer',
            'water_target_ml' => 'integer',
            'is_water_target_auto' => 'boolean',
            'protein_target_g' => 'float',
            'carbs_target_g' => 'float',
            'fat_target_g' => 'float',
            'updated_at' => 'datetime',
            'target_weight_kg' => 'float',
            'has_disease' => 'boolean',
            'has_injury' => 'boolean',
            'uses_medication' => 'boolean',
            'has_allergy' => 'boolean',
            'profile_completed_at' => 'datetime',
            'sleep_hours' => 'integer',
            'nutrition_quality' => 'integer',
            'available_daily_time_mins' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Retorna os objetivos disponíveis no sistema.
     */
    public static function getAvailableGoals(): array
    {
        return [
            'lose_aggressive' => [
                'label' => 'Emagrecimento Agressivo',
                'description' => 'Foco em perda rápida de gordura (déficit maior).',
                'icon' => 'fire',
            ],
            'lose' => [
                'label' => 'Emagrecimento Sustentável',
                'description' => 'Perda de gordura constante e saudável.',
                'icon' => 'leaf',
            ],
            'recomp' => [
                'label' => 'Recomposição Corporal',
                'description' => 'Perder gordura e ganhar músculo ao mesmo tempo.',
                'icon' => 'repeat',
            ],
            'maintain' => [
                'label' => 'Saúde e Bem-Estar',
                'description' => 'Foco em longevidade, equilíbrio e manutenção da saúde.',
                'icon' => 'heart',
            ],
            'gain' => [
                'label' => 'Hipertrofia / Bulking',
                'description' => 'Foco total em ganho de massa muscular.',
                'icon' => 'dumbbell',
            ],
            'performance' => [
                'label' => 'Performance Atlética',
                'description' => 'Suporte energético para treinos de alta intensidade.',
                'icon' => 'bolt',
            ],
        ];
    }
}
