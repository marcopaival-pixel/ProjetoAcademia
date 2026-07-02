<?php

namespace App\Http\Controllers;

use App\Models\PainRecord;
use App\Models\User;
use App\Support\PatientAccessGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PainMappingController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $targetUserId = $user->id;

        if ($user->isProfessional()) {
            $activePatientId = PatientAccessGuard::resolveActivePatientId($user);
            if ($activePatientId) {
                $targetUserId = $activePatientId;
            }
        }

        $painRecords = PainRecord::where('user_id', $targetUserId)
            ->orderBy('assessment_date', 'desc')
            ->get();

        $targetUser = User::find($targetUserId);

        return view('pain-mapping.index', compact('painRecords', 'targetUser'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $targetUserId = $user->id;

        if ($user->isProfessional()) {
            $activePatientId = PatientAccessGuard::resolveActivePatientId($user);
            if ($activePatientId) {
                $targetUserId = $activePatientId;
            }
        }

        $validated = $request->validate([
            'pain_points' => 'required|array',
            'eva_level' => 'required|integer|min:0|max:10',
            'notes' => 'nullable|string',
            'assessment_date' => 'required|date',
        ]);

        PainRecord::create([
            'user_id' => $targetUserId,
            'professional_id' => $user->isProfessional() ? $user->id : null,
            'pain_points' => $validated['pain_points'],
            'eva_level' => (int) $validated['eva_level'],
            'notes' => $validated['notes'] ?? null,
            'assessment_date' => $validated['assessment_date'],
        ]);

        return redirect()->route('pain-mapping.index')->with('success', 'Registro de dor adicionado com sucesso.');
    }

    public function destroy(PainRecord $painRecord): RedirectResponse
    {
        $user = Auth::user();
        
        // Ensure access control check
        if ((int) $painRecord->user_id !== (int) $user->id && !$user->isProfessional()) {
            abort(403);
        }

        if ($user->isProfessional()) {
            $hasLink = $user->patients()->where('users.id', $painRecord->user_id)->exists();
            if (!$hasLink) {
                abort(403);
            }
        }

        $painRecord->delete();

        return redirect()->route('pain-mapping.index')->with('success', 'Registro de dor removido com sucesso.');
    }
}
