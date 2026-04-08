<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use App\Models\UserConsent;

class RegisterController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['required', 'accepted'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
            ]);
            
            $onboarding = session('onboarding_data', []);
            
            DB::table('user_profiles')->insert([
                'user_id' => $user->id,
                'age' => $onboarding['age'] ?? null,
                'gender' => $onboarding['gender'] ?? null,
                'weight' => $onboarding['weight'] ?? null,
                'height' => $onboarding['height'] ?? null,
                'target_weight' => $onboarding['target_weight'] ?? null,
                'activity_level' => $onboarding['activity_level'] ?? null,
            ]);

            // Registrar Consentimento Inicial (LGPD)
            UserConsent::create([
                'user_id' => $user->id,
                'consent_type' => 'privacy_policy_and_terms',
                'version' => '1.0',
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);

            return $user;
        });

        // Limpar dados de onboarding após o uso
        session()->forget('onboarding_data');

        $request->session()->regenerate();
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
