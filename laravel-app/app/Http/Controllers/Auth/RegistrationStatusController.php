<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationStatusController extends Controller
{
    public function pending(): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->isAdministrator()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->registration_approval_status === 'approved') {
            return redirect()->route($user->hasRole('professional') ? 'professional.dashboard' : 'dashboard');
        }
        if ($user->registration_approval_status === 'rejected') {
            return redirect()->route('registration.rejected');
        }

        return view('auth.registration-pending');
    }

    public function rejected(): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->isAdministrator()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->registration_approval_status !== 'rejected') {
            return redirect()->route(
                $user->registration_approval_status === 'pending' ? 'registration.pending' : 'dashboard'
            );
        }

        return view('auth.registration-rejected');
    }
}
