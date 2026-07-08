<?php

namespace App\Http\Controllers\Representative;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('representative.profile.index');
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->getAttribute('id'),
            'pix_key' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        $user->setAttribute('name', $validated['name']);
        $user->setAttribute('email', $validated['email']);
        if (isset($validated['pix_key'])) {
            $user->setAttribute('pix_key', $validated['pix_key']); // Ensure this column exists in users table, or move to profile if needed
        }
        
        if (!empty($validated['password'])) {
            $user->password_hash = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('representative.profile.index')->with('success', 'Perfil atualizado com sucesso!');
    }
}
