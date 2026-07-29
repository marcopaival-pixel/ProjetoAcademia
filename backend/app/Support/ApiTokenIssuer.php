<?php

namespace App\Support;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class ApiTokenIssuer
{
    public static function ttlDays(): int
    {
        return max(1, (int) config('sanctum.token_expiration_days', 30));
    }

    public static function expiresAt(): \DateTimeInterface
    {
        return now()->addDays(self::ttlDays());
    }

    public static function issue(User $user, string $name, array $abilities = ['*']): NewAccessToken
    {
        return $user->createToken($name, $abilities, self::expiresAt());
    }

    /**
     * @return array{token_type: string, access_token: string, expires_at: string}
     */
    public static function payload(NewAccessToken $token): array
    {
        $expiresAt = $token->accessToken->expires_at ?? self::expiresAt();

        return [
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'expires_at' => $expiresAt instanceof \DateTimeInterface
                ? $expiresAt->format(\DateTimeInterface::ATOM)
                : (string) $expiresAt,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function response(User $user, string $name, array $userFields = []): array
    {
        $token = self::issue($user, $name);

        return array_merge(self::payload($token), [
            'user' => array_merge([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ], $userFields),
        ]);
    }
}
