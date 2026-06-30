<?php

namespace App\Services\Lgpd;

use App\Models\DeviceToken;
use App\Models\ShopCart;
use App\Models\ShopWishlist;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class LgpdUserAnonymizationService
{
    public const STATUS_ANONYMIZED = 'ANONIMIZADO';

    /**
     * Anonimiza PII do utilizador preservando o registo para integridade referencial
     * (pagamentos, comissões, agendamentos, etc.).
     */
    public function anonymize(User $user, ?User $actor = null, ?string $reason = null): void
    {
        if ($user->isAnonymized()) {
            throw new \RuntimeException('Utilizador já anonimizado.');
        }

        if ($user->is_admin || $user->isAdministrator()) {
            throw new \RuntimeException('Não é permitido anonimizar administradores.');
        }

        DB::transaction(function () use ($user, $actor, $reason) {
            $this->cancelActiveSubscriptions($user);
            $this->revokeAccessCredentials($user);
            $this->purgeOptionalPersonalData($user);
            $this->anonymizeProfile($user);
            $this->anonymizeUserRecord($user);

            UserConsent::create([
                'user_id' => $user->id,
                'consent_type' => 'account_anonymization',
                'version' => '1.0',
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->header('User-Agent'),
            ]);

            if ($reason !== null && $reason !== '') {
                DB::table('admin_logs')->insert([
                    'user_id' => $actor?->id ?? $user->id,
                    'action' => 'Anonimização de conta (LGPD)',
                    'ip_address' => request()?->ip(),
                    'payload' => json_encode([
                        'target_user_id' => $user->id,
                        'reason' => $reason,
                        'performed_by' => $actor?->id,
                    ]),
                    'created_at' => now(),
                ]);
            }
        });
    }

    private function cancelActiveSubscriptions(User $user): void
    {
        $terminalStatuses = [
            Subscription::STATUS_CANCELLED,
            Subscription::STATUS_EXPIRED,
            Subscription::STATUS_BLOCKED,
        ];

        $user->subscriptions()
            ->whereNotIn('status', $terminalStatuses)
            ->update([
                'status' => Subscription::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'reason_for_suspension' => 'Conta anonimizada (LGPD)',
            ]);
    }

    private function revokeAccessCredentials(User $user): void
    {
        PersonalAccessToken::where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->delete();

        DeviceToken::where('user_id', $user->id)->delete();

        DB::table('sessions')->where('user_id', $user->id)->delete();
    }

    private function purgeOptionalPersonalData(User $user): void
    {
        DB::table('food_entries')->where('user_id', $user->id)->delete();
        DB::table('exercise_entries')->where('user_id', $user->id)->delete();
        DB::table('water_entries')->where('user_id', $user->id)->delete();
        DB::table('weight_entries')->where('user_id', $user->id)->delete();

        ShopCart::where('user_id', $user->id)->each(function (ShopCart $cart) {
            $cart->items()->delete();
            $cart->delete();
        });

        ShopWishlist::where('user_id', $user->id)->delete();
    }

    private function anonymizeProfile(User $user): void
    {
        $profile = $user->profile;

        if ($profile === null) {
            return;
        }

        $profile->update([
            'birth_date' => null,
            'address' => null,
            'city' => null,
            'state' => null,
            'disease_details' => null,
            'injury_details' => null,
            'medication_details' => null,
            'allergy_details' => null,
            'emergency_contact_name' => null,
            'emergency_contact_phone' => null,
            'fitness_notes' => null,
            'has_disease' => false,
            'has_injury' => false,
            'uses_medication' => false,
            'has_allergy' => false,
        ]);
    }

    private function anonymizeUserRecord(User $user): void
    {
        $placeholderEmail = 'anonimizado+'.$user->id.'@invalid.local';

        $user->forceFill([
            'name' => 'Utilizador Anonimizado #'.$user->id,
            'username' => 'anon_'.$user->id,
            'email' => $placeholderEmail,
            'cpf' => null,
            'cnpj' => null,
            'phone' => null,
            'google_id' => null,
            'provider' => null,
            'avatar' => null,
            'qr_code_path' => null,
            'professional_code' => null,
            'email_verification_token' => null,
            'email_verified_at' => null,
            'email_verified' => false,
            'is_premium' => false,
            'premium_expires_at' => null,
            'status' => self::STATUS_ANONYMIZED,
        ]);

        $user->password_hash = Hash::make(Str::random(64));
        $user->save();
    }
}
