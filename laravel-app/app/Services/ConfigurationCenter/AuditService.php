<?php

namespace App\Services\ConfigurationCenter;

use App\Models\AuditLog;
use App\Models\RecordVersion;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log($model, string $action, array $oldValues = null, array $newValues = null)
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'clinic_id' => TenantContext::get() ?? Auth::user()?->clinic_id,
            'academy_company_id' => TenantContext::getCompanyId() ?? Auth::user()?->academy_company_id,
            'entity_type' => get_class($model),
            'entity_id' => $model->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public static function createVersion($model, string $notes = null)
    {
        $lastVersion = RecordVersion::where('entity_type', get_class($model))
            ->where('entity_id', $model->getKey())
            ->max('version_number') ?? 0;

        return RecordVersion::create([
            'user_id' => Auth::id(),
            'clinic_id' => TenantContext::get() ?? Auth::user()?->clinic_id,
            'academy_company_id' => TenantContext::getCompanyId() ?? Auth::user()?->academy_company_id,
            'entity_type' => get_class($model),
            'entity_id' => $model->getKey(),
            'version_number' => $lastVersion + 1,
            'data' => $model->toArray(),
            'notes' => $notes,
        ]);
    }
}
