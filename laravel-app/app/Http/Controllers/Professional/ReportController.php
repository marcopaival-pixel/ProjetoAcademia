<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Services\ReportMonetizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportMonetizationService $monetizationService
    ) {}

    /**
     * Index do módulo de relatórios para profissionais.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $reports = $this->monetizationService->getAvailableReports($user);

        return view('professional.reports.index', [
            'reports' => $reports,
            'isPremium' => $this->monetizationService->hasPremium($user),
        ]);
    }

    /**
     * Relatórios premium específicos (placeholder) — apenas IDs listados em getAvailableReports.
     */
    public function show(Request $request, string $type): RedirectResponse|View
    {
        $user = $request->user();

        if (! $this->monetizationService->hasPremium($user)) {
            return redirect()->route('professional.reports.index')
                ->with('premium_required', true);
        }

        $allowed = $this->monetizationService->premiumReportIdsForUser($user);
        if (! in_array($type, $allowed, true)) {
            abort(404);
        }

        $this->monetizationService->logGeneration($user, $type);

        return view('errors.coming-soon', ['feature' => $type]);
    }
}
