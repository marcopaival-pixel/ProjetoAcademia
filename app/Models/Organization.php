<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Organization extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'type',
        'owner_id',
        'tax_id',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role', 'is_active')
            ->withTimestamps();
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'organization_patient')
            ->withPivot('internal_code')
            ->withTimestamps();
    }
}
