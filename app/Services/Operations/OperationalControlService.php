<?php

namespace App\Services\Operations;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OperationalControlService
{
    protected $cacheKey = 'operational_settings';
    protected $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('framework/operational_settings.json');
    }

    /**
     * Get current operational settings.
     */
    public function getSettings(): array
    {
        return Cache::rememberForever($this->cacheKey, function () {
            if (file_exists($this->filePath)) {
                return json_decode(file_get_contents($this->filePath), true);
            }

            return [
                'maintenance_mode' => 'off', // off, total, operable
                'maintenance_message' => 'O sistema está em manutenção para melhorias.',
                'read_only_mode' => false,
                'last_updated' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Update operational settings.
     */
    public function updateSettings(array $newSettings): void
    {
        $settings = array_merge($this->getSettings(), $newSettings);
        $settings['last_updated'] = now()->toIso8601String();
        
        // Save to file for hard persistence
        file_put_contents($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));
        
        // Update cache
        Cache::forever($this->cacheKey, $settings);

        Log::info('Operational settings updated', $settings);
    }

    /**
     * Is the system in maintenance mode?
     */
    public function isMaintenance(): bool
    {
        return $this->getSettings()['maintenance_mode'] !== 'off';
    }

    /**
     * Is the system in read-only mode?
     */
    public function isReadOnly(): bool
    {
        return $this->getSettings()['read_only_mode'] === true;
    }

    /**
     * Check if a user can bypass maintenance.
     */
    public function canBypass($user): bool
    {
        if (!$user) return false;
        
        // Assuming administrators have a specific method or property
        return method_exists($user, 'isAdministrator') ? $user->isAdministrator() : false;
    }
}
