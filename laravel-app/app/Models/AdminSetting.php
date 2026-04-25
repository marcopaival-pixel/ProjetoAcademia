<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $table = 'admin_settings';

    protected $fillable = [
        'key',
        'value',
        'label',
        'type',
    ];

    public static function get($key, $default = null)
    {
        try {
            $setting = self::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        } catch (\Throwable) {
            // Evita HTTP 500 nas views (layouts chamam get() em todo o site) se a BD estiver indisponível.
            return $default;
        }
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
