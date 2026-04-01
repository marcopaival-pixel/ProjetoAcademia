<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
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
        'goal',
        'daily_calorie_target',
        'protein_target_g',
        'carbs_target_g',
        'fat_target_g',
        'water_target_ml',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'height_cm' => 'integer',
            'daily_calorie_target' => 'integer',
            'water_target_ml' => 'integer',
            'protein_target_g' => 'float',
            'carbs_target_g' => 'float',
            'fat_target_g' => 'float',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
