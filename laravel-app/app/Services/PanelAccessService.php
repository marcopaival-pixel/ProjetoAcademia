<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PanelAccessService
{
    public const PANEL_ADMIN = 'admin';

    public const PANEL_PROFESSIONAL = 'professional';

    public const PANEL_PATIENT = 'patient';

    public const PANEL_STUDENT = 'student';

    public const PANEL_REPRESENTATIVE = 'representative';

    /** @var array<string, list<string>> */
    private const ACTIVE_ROLE_TO_PANEL = [
        'admin' => [self::PANEL_ADMIN],
        'gestor' => [self::PANEL_ADMIN],
        'manager' => [self::PANEL_PROFESSIONAL],
        'finance' => [self::PANEL_ADMIN],
        'receptionist' => [self::PANEL_ADMIN],
        'professional' => [self::PANEL_PROFESSIONAL],
        'instructor' => [self::PANEL_PROFESSIONAL],
        'supervisor' => [self::PANEL_PROFESSIONAL],
        'nutricionista' => [self::PANEL_PROFESSIONAL],
        'personal' => [self::PANEL_PROFESSIONAL],
        'paciente' => [self::PANEL_PATIENT],
        'aluno' => [self::PANEL_STUDENT],
        'representative' => [self::PANEL_REPRESENTATIVE],
    ];

    public function resolveActiveRole(User $user): string
    {
        $sessionRole = session('active_role');
        if (is_string($sessionRole) && $sessionRole !== '') {
            return $sessionRole;
        }

        if ($user->isAdministrator() && $user->roles->isEmpty()) {
            return 'admin';
        }

        if ($user->roles->count() === 1) {
            return (string) $user->roles->first()->name;
        }

        if ($user->isAdministrator()) {
            return 'admin';
        }

        return (string) ($user->roles->first()?->name ?? 'aluno');
    }

    public function panelForActiveRole(string $activeRole, User $user): string
    {
        if ($activeRole === 'admin' || ($activeRole === '' && $user->isAdministrator())) {
            return self::PANEL_ADMIN;
        }

        foreach (self::ACTIVE_ROLE_TO_PANEL as $role => $panels) {
            if ($activeRole === $role) {
                return $panels[0];
            }
        }

        return self::PANEL_STUDENT;
    }

    public function currentPanel(User $user): string
    {
        return $this->panelForActiveRole($this->resolveActiveRole($user), $user);
    }

    public function detectPanelFromPath(string $path): ?string
    {
        $path = '/'.ltrim(strtolower($path), '/');

        if (str_starts_with($path, '/admin')) {
            return self::PANEL_ADMIN;
        }

        if (str_starts_with($path, '/professional')) {
            return self::PANEL_PROFESSIONAL;
        }

        if (str_starts_with($path, '/representative')) {
            return self::PANEL_REPRESENTATIVE;
        }

        if ($this->isStudentPatientReportPath($path)) {
            return self::PANEL_STUDENT;
        }

        if ($this->isPatientPath($path)) {
            return self::PANEL_PATIENT;
        }

        if ($this->isStudentPath($path)) {
            return self::PANEL_STUDENT;
        }

        return null;
    }

    public function isBypassPath(string $path): bool
    {
        $path = '/'.ltrim(strtolower($path), '/');

        $exact = [
            '/',
            '/login',
            '/register',
            '/logout',
            '/forgot-password',
            '/health',
            '/up',
            '/theme',
            '/plano',
            '/business',
            '/profile',
            '/profile/selection',
            '/profile/select',
            '/clinic-selection',
            '/clinic-select',
            '/notifications/unread-counts',
            '/confirmar-email/sucesso',
        ];

        if (in_array($path, $exact, true)) {
            return true;
        }

        $prefixes = [
            '/reset-password',
            '/auth/google',
            '/confirmar-email/',
            '/verify-email/',
            '/cadastro/',
            '/registration/',
            '/password/',
            '/legal/',
            '/demo/',
            '/proposal/',
            '/payment/webhook',
            '/omnichannel/webhook',
            '/mp/',
            '/checkout/',
            '/ref/',
            '/validar-documento',
            '/api/marketing/',
            '/api/food/',
            '/ativar-conta/',
            '/patient/access',
            '/patient/professionals/search',
            '/patient/professionals/',
            '/patient/subscription',
            '/privacy/',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function homeRouteForPanel(string $panel): string
    {
        return match ($panel) {
            self::PANEL_ADMIN => route('admin.dashboard'),
            self::PANEL_PROFESSIONAL => route('professional.dashboard'),
            self::PANEL_PATIENT => route('patient.portal'),
            self::PANEL_REPRESENTATIVE => route('representative.dashboard'),
            default => route('dashboard'),
        };
    }

    public function sanitizeIntendedUrl(?string $url, string $panel, string $fallback): string
    {
        if ($url === null || $url === '') {
            return $fallback;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return $fallback;
        }

        if (str_contains($path, '/login')) {
            return $fallback;
        }

        $targetPanel = $this->detectPanelFromPath($path);

        if ($targetPanel === null) {
            return $panel === self::PANEL_STUDENT ? $url : $fallback;
        }

        return $targetPanel === $panel ? $url : $fallback;
    }

    public function userCanUsePanel(User $user, string $panel): bool
    {
        return match ($panel) {
            self::PANEL_ADMIN => $user->hasAdminPanelAccess(),
            self::PANEL_PROFESSIONAL => $user->isProfessional(),
            self::PANEL_PATIENT => $user->hasRole('paciente'),
            self::PANEL_STUDENT => $user->hasRole('aluno'),
            self::PANEL_REPRESENTATIVE => $user->hasRole('representative'),
            default => false,
        };
    }

    public function wrongPanelRedirect(Request $request, User $user): ?RedirectResponse
    {
        if (! $this->shouldEnforce($request, $user)) {
            return null;
        }

        if ($user->hasAdminPanelAccess()) {
            return null;
        }

        $pathPanel = $this->detectPanelFromPath('/'.$request->path());
        if ($pathPanel === null) {
            return null;
        }

        if (! $this->userCanUsePanel($user, $pathPanel)) {
            if (in_array($pathPanel, [self::PANEL_REPRESENTATIVE, self::PANEL_ADMIN], true)) {
                return null;
            }
        }

        $userPanel = $this->panelForActiveRole($this->resolveActiveRole($user), $user);
        if ($pathPanel === $userPanel) {
            return null;
        }

        if ($pathPanel === self::PANEL_ADMIN && ! $user->hasAdminPanelAccess()) {
            return null;
        }

        if (! $this->userMayEnterPanel($user, $userPanel)) {
            return null;
        }

        if (session('is_demo_mode')) {
            return redirect($this->homeRouteForPanel($userPanel));
        }

        return redirect($this->homeRouteForPanel($userPanel))
            ->with('error', 'Esta área pertence a outro perfil. Você foi redirecionado para o seu painel.');
    }

    public function userMayEnterPanel(User $user, string $panel): bool
    {
        return match ($panel) {
            self::PANEL_ADMIN => $user->hasAdminPanelAccess(),
            self::PANEL_PROFESSIONAL => $user->isProfessional(),
            self::PANEL_PATIENT => $user->hasRole('paciente'),
            self::PANEL_REPRESENTATIVE => $user->hasRole('representative'),
            self::PANEL_STUDENT => $user->hasRole('aluno'),
            default => false,
        };
    }

    public function shouldEnforce(Request $request, ?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if ($request->routeIs(
            'login',
            'login.*',
            'register',
            'register.*',
            'password.*',
            'verification.*',
            'email-verification.*',
            'registration.*',
            'profile.selection',
            'profile.select',
            'clinic.selector',
            'clinic.select',
            'logout',
            'admin.logout',
            'admin.login',
            'admin.login.submit',
            'home',
            'health.check',
        )) {
            return false;
        }

        return ! $this->isBypassPath($request->path());
    }

    private function isPatientPath(string $path): bool
    {
        return str_starts_with($path, '/patient');
    }

    private function isStudentPatientReportPath(string $path): bool
    {
        return $path === '/patient/reports' || str_starts_with($path, '/patient/reports/');
    }

    private function isStudentPath(string $path): bool
    {
        $prefixes = [
            '/dashboard',
            '/diary',
            '/exercise',
            '/exercise-catalog',
            '/exercises-catalog',
            '/nutrition',
            '/assessments',
            '/body-analysis',
            '/chat',
            '/community',
            '/onboarding',
            '/training',
            '/evolution',
            '/agenda',
            '/hydration',
            '/report',
            '/export',
            '/smart-stack',
            '/workout',
            '/messages',
            '/support',
            '/kb',
            '/health-metrics',
            '/active-rest',
            '/load-progression',
            '/leaderboard',
            '/menu-preferences',
            '/communication-groups',
            '/api/hydration',
            '/api/exercise',
            '/nutrition/api',
            '/global-search',
            '/muscles/search',
            '/validate-report',
            '/workout-photo-import',
            '/smart-query',
            '/trophies',
            '/supplements',
            '/meal-templates',
            '/index.php',
        ];

        foreach ($prefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }
}
