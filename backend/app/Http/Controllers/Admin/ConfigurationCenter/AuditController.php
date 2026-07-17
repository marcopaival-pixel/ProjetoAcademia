<?php

namespace App\Http\Controllers\Admin\ConfigurationCenter;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\RecordVersion;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();
        $this->applyTenantScope($query);

        if ($request->has('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->has('entity_id')) {
            $query->where('entity_id', $request->entity_id);
        }

        $logs = $query->paginate(20);

        return view('admin.configuration-center.audit.index', compact('logs'));
    }

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        return view('admin.configuration-center.audit.show', compact('log'));
    }

    public function versions(string $entityType, $entityId)
    {
        $versionsQuery = RecordVersion::with('user')
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId);
        $this->applyTenantScope($versionsQuery);
        $versions = $versionsQuery->orderBy('version_number', 'desc')->get();

        return view('admin.configuration-center.audit.versions', compact('versions', 'entityType', 'entityId'));
    }

    /**
     * Administradores globais veem tudo; demais utilizadores ficam limitados à empresa ativa.
     */
    private function applyTenantScope($query): void
    {
        $user = Auth::user();
        if (! $user || ($user->is_admin && ! session()->has('impersonated_clinic_id'))) {
            return;
        }

        $companyId = TenantContext::getCompanyId();
        if ($companyId) {
            $query->where('academy_company_id', $companyId);
        }
    }
}
