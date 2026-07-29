<?php

namespace App\Observers;

use App\Services\ConfigurationCenter\AuditService;
use Illuminate\Database\Eloquent\Model;

class CriticalModelAuditObserver
{
    /** @var list<string> */
    private const HIDDEN = [
        'password_hash',
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'email_verification_token',
    ];

    public function created(Model $model): void
    {
        AuditService::log($model, 'created', null, $this->filter($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $changes = $this->filter($model->getChanges());

        if ($changes === []) {
            return;
        }

        $original = [];
        foreach (array_keys($changes) as $key) {
            $original[$key] = $model->getOriginal($key);
        }

        AuditService::log($model, 'updated', $this->filter($original), $changes);
    }

    public function deleted(Model $model): void
    {
        AuditService::log($model, 'deleted', $this->filter($model->getAttributes()), null);
    }

    /**
     * @param  array<string, mixed>|null  $values
     * @return array<string, mixed>
     */
    private function filter(?array $values): array
    {
        if ($values === null) {
            return [];
        }

        return array_diff_key($values, array_flip(self::HIDDEN));
    }
}
