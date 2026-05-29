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
            // Cache por 24 horas (ajustar conforme necessidade)
            return \Cache::remember("admin_setting_{$key}", 86400, function() use ($key, $default) {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function set($key, $value)
    {
        \Cache::forget("admin_setting_{$key}");
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function isTrue($key, $default = true): bool
    {
        $val = self::get($key, $default ? 'true' : 'false');
        return $val === 'true' || $val === '1' || $val === true || $val === 'on';
    }
}
