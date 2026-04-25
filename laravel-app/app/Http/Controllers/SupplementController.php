<?php

namespace App\Http\Controllers;

use App\Models\Supplement;
use Illuminate\Http\Request;

class SupplementController extends Controller
{
    public function take(Supplement $supplement)
    {
        if ($supplement->user_id !== auth()->id()) {
            abort(403);
        }

        $supplement->update(['last_taken_at' => now()]);

        // Log the intake for adherence tracking
        \App\Models\SupplementLog::create([
            'user_id' => auth()->id(),
            'supplement_id' => $supplement->id,
            'taken_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Suplemento ' . $supplement->name . ' marcado como tomado!',
            'last_taken' => $supplement->last_taken_at->diffForHumans()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'nullable|string|max:50',
            'unit' => 'nullable|string|max:20',
            'time_of_day' => 'nullable|string|max:50',
        ]);

        $supplement = Supplement::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'dosage' => $data['dosage'],
            'unit' => $data['unit'] ?? 'g',
            'time_of_day' => $data['time_of_day'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Suplemento adicionado ao seu Stack!');
    }
    public function destroy(Supplement $supplement)
    {
        if ($supplement->user_id !== auth()->id()) {
            abort(403);
        }

        $supplement->delete();

        return back()->with('success', 'Suplemento removido com sucesso!');
    }
}
