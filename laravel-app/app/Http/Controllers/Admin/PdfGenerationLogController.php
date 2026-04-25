<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PdfGenerationLog;
use App\Models\PdfTemplate;
use Illuminate\View\View;

class PdfGenerationLogController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', PdfTemplate::class);

        $logs = PdfGenerationLog::query()
            ->with(['user', 'template'])
            ->orderByDesc('id')
            ->paginate(40);

        return view('admin.pdf-templates.logs', compact('logs'));
    }
}
