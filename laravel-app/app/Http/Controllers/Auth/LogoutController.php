<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthAuditLog;
use App\Services\Operations\AuthAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $email = Auth::user()?->email;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        app(AuthAuditService::class)->log(
            AuthAuditLog::EVENT_LOGOUT,
            $userId,
            $email,
            true,
            $request,
        );

        return redirect()->route('login');
    }
}
