<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingModule extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lessons()
    {
        return $this->hasMany(TrainingLesson::class, 'module_id')->orderBy('order');
    }
}
