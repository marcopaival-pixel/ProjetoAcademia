<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\ConfiguracaoEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MailConfigService
{
    /**
     * Aplica configuração de e-mail: por empresa (se existir e ativa) ou definições globais em admin_settings.
     */
    public static function apply(?int $academyCompanyId = null): void
    {
        try {
            if (! Schema::hasTable('admin_settings')) {
                return;
            }

            if ($academyCompanyId !== null
                && Schema::hasTable('configuracao_email')) {
                $cfg = ConfiguracaoEmail::where('empresa_id', $academyCompanyId)->where('ativo', true)->first();
                if ($cfg !== null) {
                    self::applyResolvedSettings($cfg->resolveMailSettings());

                    return;
                }
            }

            self::applyGlobalAdminSettings();
        } catch (\Throwable $e) {
            Log::error('Erro ao aplicar configurações de e-mail: '.$e->getMessage());
        }
    }

    /**
     * @param  array{
     *   host: string|null,
     *   port: int,
     *   encryption: string|null,
     *   username: string|null,
     *   password: string|null,
     *   from_address: string|null,
     *   from_name: string|null,
     *   timeout: int
     * }  $s
     */
    public static function applyResolvedSettings(array $s): void
    {
        if (empty($s['host'])) {
            self::applyGlobalAdminSettings();

            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $s['host']);
        Config::set('mail.mailers.smtp.port', $s['port']);
        Config::set('mail.mailers.smtp.encryption', $s['encryption']);
        Config::set('mail.mailers.smtp.username', $s['username']);
        Config::set('mail.mailers.smtp.password', $s['password']);
        Config::set('mail.mailers.smtp.timeout', $s['timeout']);

        if (! empty($s['from_address'])) {
            Config::set('mail.from.address', $s['from_address']);
            Config::set('mail.from.name', $s['from_name'] ?? config('app.name'));
        }
    }

    public static function applyGlobalAdminSettings(): void
    {
        $encryptedPassword = AdminSetting::get('mail_password');
        $password = null;

        if ($encryptedPassword) {
            try {
                $password = Crypt::decryptString($encryptedPassword);
            } catch (\Exception $e) {
                $password = $encryptedPassword;
            }
        }

        $settings = [
            'mail_host' => AdminSetting::get('mail_host'),
            'mail_port' => AdminSetting::get('mail_port', '587'),
            'mail_username' => AdminSetting::get('mail_username'),
            'mail_password' => $password,
            'mail_encryption' => AdminSetting::get('mail_encryption', 'tls'),
            'mail_from_address' => AdminSetting::get('mail_from_address'),
            'mail_from_name' => AdminSetting::get('mail_from_name', config('app.name')),
        ];

        if (! $settings['mail_host']) {
            return;
        }

        $enc = $settings['mail_encryption'];
        $encryption = ($enc === 'none' || $enc === '') ? null : $enc;

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $settings['mail_host']);
        Config::set('mail.mailers.smtp.port', $settings['mail_port']);
        Config::set('mail.mailers.smtp.encryption', $encryption);
        Config::set('mail.mailers.smtp.username', $settings['mail_username']);
        Config::set('mail.mailers.smtp.password', $settings['mail_password']);
        Config::set('mail.mailers.smtp.timeout', null);

        Config::set('mail.from.address', $settings['mail_from_address']);
        Config::set('mail.from.name', $settings['mail_from_name']);
    }
}
