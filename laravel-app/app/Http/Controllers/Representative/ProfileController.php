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
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'pix_key' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['pix_key'])) {
            $user->pix_key = $validated['pix_key']; // Ensure this column exists in users table, but keeping it dynamic or update the logic if it's in a profile table
        }
        
        if (!empty($validated['password'])) {
            $user->password_hash = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('representative.profile.index')->with('success', 'Perfil atualizado com sucesso!');
    }
}
