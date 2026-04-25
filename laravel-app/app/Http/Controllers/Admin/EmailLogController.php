<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogEnvioEmail;
use Illuminate\View\View;

class EmailLogController extends Controller
{
    public function index(): View
    {
        $logs = LogEnvioEmail::query()
            ->with(['empresa', 'usuario'])
            ->orderByDesc('data_envio')
            ->paginate(50);

        return view('admin.email.logs-index', compact('logs'));
    }
}
