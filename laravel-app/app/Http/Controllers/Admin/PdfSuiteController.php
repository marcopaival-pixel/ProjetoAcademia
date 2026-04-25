<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PdfSuiteController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $u = $request->user();
            if ($u === null) {
                abort(403);
            }
            if (
                $u->isAdministrator()
                || $u->hasPermission('pdf.templates.manage')
                || $u->hasPermission('pdf.documents.generate')
                || $u->hasPermission('pdf.history.view')
                || $u->hasPermission('pdf.companies.manage')
                || $u->hasPermission('pdf.integrations.view')
            ) {
                return $next($request);
            }
            abort(403);
        });
    }

    public function index(): View
    {
        return view('admin.pdf-suite.hub');
    }
}
