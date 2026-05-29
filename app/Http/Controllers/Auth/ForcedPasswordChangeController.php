<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminLog;

class ForcedPasswordChangeController extends Controller
{
    public function show()
    {
        if (!auth()->user()->force_password_change) {
            return redirect()->route('dashboard');
        }

        return view('auth.passwords.force_change');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->force_password_change) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'password' => [
                'required', 
                'min:8', 
                'confirmed',
                'regex:/[A-Z]/', 
                'regex:/[0-9]/', 
                'regex:/[!@#$%^&*(),.?":{}|<>]/',
            ],
        ], [
            'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, um número e um caractere especial.',
        ]);

        $user->password_hash = Hash::make($request->input('password'));
        $user->force_password_change = false;
        $user->temp_password_expires_at = null;
        $user->save();

        AdminLog::create([
            'user_id' => $user->id,
            'action' => "Alteração de senha obrigatória concluída",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Sua senha foi atualizada com sucesso. Bem-vindo!');
    }
}
