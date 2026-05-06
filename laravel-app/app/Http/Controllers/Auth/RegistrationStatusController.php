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
        if ($user->registration_approval_status !== 'rejected' && $user->status !== 'RECUSADO') {
            return redirect()->route(
                $user->isRegistrationPending() ? 'registration.pending' : 'dashboard'
            );
        }

        return view('auth.registration-rejected');
    }

    public function representativePending(): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }
        
        if ($user->isActive()) {
            return redirect()->route('representative.dashboard');
        }

        if (! $user->isRepresentativePending()) {
             return redirect()->route($user->isRegistrationPending() ? 'registration.pending' : 'dashboard');
        }

        return view('auth.representative-pending');
    }

    public function track(): View
    {
        return view('auth.registration-track');
    }

    public function search(\Illuminate\Http\Request $request): View
    {
        $request->validate([
            'search' => 'required|string',
        ]);

        $search = $request->input('search');
        $normalizedSearch = \App\Support\Cpf::normalize($search);

        $user = \App\Models\User::where('email', $search)
            ->orWhere('cpf', $normalizedSearch)
            ->first();

        return view('auth.registration-track', [
            'user' => $user,
            'search' => $search
        ]);
    }
}
