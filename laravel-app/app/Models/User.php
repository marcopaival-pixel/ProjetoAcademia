<?php

namespace App\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'email',
        'password_hash',
        'is_premium',
        'premium_expires_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
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
}
