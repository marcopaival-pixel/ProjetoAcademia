<?php

namespace App\Http\Controllers;

use App\Models\UserConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivacyController extends Controller
{
    /** Public Legal Pages - Unified Hub */
    public function privacyPolicy(): View { return view('legal.terms', ['activeTab' => 'privacy']); }
    public function termsOfUse(): View { return view('legal.terms', ['activeTab' => 'terms']); }
    public function cookiePolicy(): View { return view('legal.terms', ['activeTab' => 'cookies']); }

    /** Data Portability: Download Data (JSON) */
    public function downloadMyData(): StreamedResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Acesso não autorizado.');
        }

        $profile = $user->profile;
        $meals = $user->foodEntries()->with('food')->get();
        $exercises = $user->exerciseEntries()->get();
        
        $loadLogs = DB::table('load_logs')->where('user_id', $user->id)->get();
        $assessments = DB::table('body_assessments')->where('user_id', $user->id)->get();
        $achievements = DB::table('achievements')->where('user_id', $user->id)->get();

        $data = [
            'personal' => [
                'name' => $user->name,
                'email' => $user->email,
                'age' => $profile->age ?? '?',
                'gender' => $profile->gender ?? '?',
                'weight' => $profile->weight ?? '?',
                'height' => $profile->height ?? '?',
            ],
            'meals' => $meals->map(fn($m) => [
                'date' => $m->date,
                'food' => $m->food->name ?? '?',
                'amount' => $m->amount,
                'calories' => $m->calories,
            ]),
            'training_logs' => $loadLogs,
            'body_assessments' => $assessments,
            'achievements' => $achievements,
            'exercises' => $exercises,
            'export_date' => now()->toDateTimeString(),
        ];

        $fileName = "my-data-" . $user->id . ".json";
        
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $fileName);
    }

    /** Right to be Forgotten: Request Deletion */
    public function requestAccountDeletion(Request $request)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = $validated['reason'] ?? 'Não informada';

        DB::table('admin_logs')->insert([
            'user_id' => Auth::id(),
            'action' => 'Solicitaçāo de exclusāo de conta (LGPD)',
            'ip_address' => $request->ip(),
            'payload' => json_encode(['reason' => $reason]),
        ]);

        return redirect()->back()->with('success', 'Sua solicitação foi registrada e será atendida em até 15 dias, conforme os prazos legais da LGPD.');
    }

    /** Acceptance Handler (for pre-registration flow or modal) */
    public function acceptConsent(Request $request)
    {
        $validated = $request->validate([
            'type' => [
                'nullable',
                'string',
                'max:50',
                Rule::in(['privacy_policy', 'terms_of_use', 'cookies', 'privacy_policy_and_terms']),
            ],
        ]);

        $user = Auth::user();
        UserConsent::create([
            'user_id' => $user->id,
            'consent_type' => $validated['type'] ?? 'privacy_policy',
            'version' => '1.0',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return response()->json(['success' => true]);
    }
}
