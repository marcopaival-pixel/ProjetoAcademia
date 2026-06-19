<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeployRelease;
use App\Services\DeployReleaseService;
use App\Support\AppVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeployReleaseController extends Controller
{
    public function __construct(
        private readonly DeployReleaseService $deployService
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', DeployRelease::class);

        $releases = DeployRelease::query()
            ->with('deployer')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $failures = DeployRelease::query()
            ->where('status', DeployRelease::STATUS_FAILED)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.deploy.index', [
            'currentVersion' => AppVersion::display(),
            'nextVersionHint' => 'v' . AppVersion::parse(AppVersion::current())[0] . '.'
                . (AppVersion::parse(AppVersion::current())[1] + 1) . '.0',
            'lastProduction' => $this->deployService->latestForEnvironment(DeployRelease::ENV_PRODUCTION),
            'lastHomolog' => $this->deployService->latestForEnvironment(DeployRelease::ENV_HOMOLOG),
            'homologPending' => $this->deployService->latestHomologPending(),
            'releases' => $releases,
            'failures' => $failures,
            'laravelVersion' => app()->version(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', DeployRelease::class);

        $validated = $request->validate([
            'version' => ['required', 'string', 'max:32', 'regex:/^v?\d+\.\d+\.\d+$/'],
            'environment' => ['required', 'in:homologacao,production'],
            'status' => ['required', 'in:pending,in_progress,success,failed'],
            'homolog_status' => ['nullable', 'in:pending,approved,rejected'],
            'impact_level' => ['required', 'in:low,medium,high'],
            'risk_level' => ['required', 'in:low,medium,high'],
            'git_branch' => ['nullable', 'string', 'max:120'],
            'git_commit' => ['nullable', 'string', 'max:64'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'failure_message' => ['nullable', 'string', 'max:5000'],
            'files_changed_count' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $validated['version'] = AppVersion::normalize($validated['version']);

        if ($validated['environment'] === DeployRelease::ENV_HOMOLOG && empty($validated['homolog_status'])) {
            $validated['homolog_status'] = DeployRelease::HOMOLOG_PENDING;
        }

        $this->deployService->record($validated, $request->user()?->id);

        return redirect()
            ->route('admin.deploy.index')
            ->with('success', 'Release registrada com sucesso.');
    }

    public function updateHomolog(Request $request, DeployRelease $deployRelease): RedirectResponse
    {
        $this->authorize('update', $deployRelease);

        $validated = $request->validate([
            'homolog_status' => ['required', 'in:approved,rejected'],
        ]);

        if ($deployRelease->environment !== DeployRelease::ENV_HOMOLOG) {
            return back()->with('error', 'Apenas releases de homologação podem ser aprovadas aqui.');
        }

        $this->deployService->updateHomologStatus($deployRelease, $validated['homolog_status']);

        return back()->with('success', 'Status de homologação atualizado.');
    }
}
