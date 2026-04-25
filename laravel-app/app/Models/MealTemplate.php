<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealTemplate extends Model
{
    use Traits\FiltersByProfessional;

    protected $fillable = [
        'user_id',
        'professional_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MealTemplateItem::class)->orderBy('position');
    }
}
