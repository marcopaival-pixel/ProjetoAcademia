<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Draft;

class DraftController extends Controller
{
    /**
     * Salva ou atualiza um rascunho de formulário.
     */
    public function store(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
            'payload' => 'required|array',
            'expires_in_minutes' => 'nullable|integer|min:1',
        ]);

        $user = Auth::user();
        
        // Define expiração padrão de 24 horas, ou baseada no request
        $expiresAt = now()->addMinutes($request->input('expires_in_minutes', 1440));

        $draft = Draft::updateOrCreate(
            [
                'user_id' => $user->id,
                'identifier' => $request->input('identifier'),
            ],
            [
                'payload' => $request->input('payload'),
                'expires_at' => $expiresAt,
            ]
        );

        return response()->json([
            'status' => 'success',
            'draft' => $draft
        ]);
    }

    /**
     * Recupera um rascunho salvo, caso não tenha expirado.
     */
    public function show($identifier)
    {
        $user = Auth::user();

        $draft = Draft::where('user_id', $user->id)
            ->where('identifier', $identifier)
            ->first();

        if (! $draft) {
            return response()->json(['message' => 'Draft not found.'], 404);
        }

        if ($draft->expires_at && $draft->expires_at->isPast()) {
            $draft->delete();
            return response()->json(['message' => 'Draft expired.'], 404);
        }

        return response()->json([
            'draft' => $draft
        ]);
    }
}
