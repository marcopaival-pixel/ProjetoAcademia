<?php

namespace App\Http\Controllers\Admin\ConfigurationCenter;

use App\Http\Controllers\Controller;
use App\Models\AdminEntity;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_entities' => AdminEntity::count(),
            'total_logs' => AuditLog::count(),
            'recent_logs' => AuditLog::with('user')->latest()->take(10)->get(),
            'entity_stats' => AdminEntity::withCount('fields')->get(),
        ];

        return view('admin.configuration-center.dashboard', compact('stats'));
    }
}
