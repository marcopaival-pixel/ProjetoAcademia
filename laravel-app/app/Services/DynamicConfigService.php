<?php

namespace App\Services;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class DynamicConfigService
{
    /**
     * Aplica configurações dinâmicas do banco de dados ao sistema.
     */
    public static function apply(): void
    {
        if (!Schema::hasTable('admin_settings')) {
            return;
        }

        self::applyAiConfig();
        self::applyWhatsAppConfig();
        self::applySystemConfig();
    }

    /**
     * Configurações globais de ambiente e localização.
     */
    protected static function applySystemConfig(): void
    {
        $timezone = AdminSetting::get('app_timezone');
        $locale = AdminSetting::get('app_locale');
        $debug = AdminSetting::get('app_debug');
        $url = AdminSetting::get('app_url');

        if ($timezone) {
            Config::set('app.timezone', $timezone);
            if (!app()->runningInConsole()) {
                date_default_timezone_set($timezone);
            }
        }

        if ($locale) {
            Config::set('app.locale', $locale);
            app()->setLocale($locale);
        }

        if ($debug !== null) {
            // Só aplica se for explicitamente definido como string 'true' ou 'false'
            $isDebug = $debug === 'true' || $debug === '1';
            Config::set('app.debug', $isDebug);
        }

        if ($url) {
            Config::set('app.url', $url);
        }
    }

    /**
     * Configurações de Inteligência Artificial (OpenAI).
     */
    protected static function applyAiConfig(): void
    {
        $apiKey = AdminSetting::get('openai_api_key');
        $model = AdminSetting::get('openai_model');
        $apiUrl = AdminSetting::get('openai_api_url');

        if ($apiKey) {
            Config::set('services.openai.api_key', $apiKey);
        }
        
        if ($model) {
            Config::set('services.openai.model', $model);
        }

        if ($apiUrl) {
            Config::set('services.openai.api_url', $apiUrl);
        }
    }

    /**
     * Configurações de WhatsApp.
     */
    protected static function applyWhatsAppConfig(): void
    {
        $driver = AdminSetting::get('whatsapp_driver');
        $apiUrl = AdminSetting::get('whatsapp_api_url');
        $token = AdminSetting::get('whatsapp_token');

        if ($driver) {
            Config::set('services.whatsapp.driver', $driver);
        }

        if ($apiUrl) {
            Config::set('services.whatsapp.api_url', $apiUrl);
        }

        if ($token) {
            Config::set('services.whatsapp.token', $token);
        }
    }
}
