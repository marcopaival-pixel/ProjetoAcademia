<?php

namespace App\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password_hash',
        'is_premium',
        'is_admin',
        'premium_expires_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'is_admin' => 'boolean',
            'premium_expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<MealTemplate, User>
     */
    public function mealTemplates(): HasMany
    {
        return $this->hasMany(MealTemplate::class, 'user_id', 'id');
    }

    public function waterEntries(): HasMany
    {
        return $this->hasMany(WaterEntry::class, 'user_id', 'id');
    }

    public function weightEntries(): HasMany
    {
        return $this->hasMany(WeightEntry::class, 'user_id', 'id');
    }

    public function foodEntries(): HasMany
    {
        return $this->hasMany(FoodEntry::class, 'user_id', 'id');
    }

    public function exerciseEntries(): HasMany
    {
        return $this->hasMany(ExerciseEntry::class, 'user_id', 'id');
    }

    public function aiChats(): HasMany
    {
        return $this->hasMany(AIChat::class, 'user_id', 'id');
    }

    public function isPremiumActive(): bool
    {
        if (! $this->is_premium) {
            return false;
        }
        $exp = $this->premium_expires_at;
        if ($exp === null) {
            return true;
        }
        try {
            $expAt = new DateTimeImmutable($exp->format('Y-m-d H:i:s'));

            return $expAt >= new DateTimeImmutable('now');
        } catch (\Exception) {
            return false;
        }
    }

    public function isAdministrator(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Acesso às funcionalidades reservadas ao Premium (export CSV, macros manuais, chat IA sem quota, etc.).
     * Administradores têm o mesmo acesso sem necessidade de assinatura.
     */
    public function hasPremiumAccess(): bool
    {
        return $this->isPremiumActive() || $this->isAdministrator();
    }
}
