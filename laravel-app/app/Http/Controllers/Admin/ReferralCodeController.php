<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralCode;
use Illuminate\Http\Request;

class ReferralCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = ReferralCode::with(['representative', 'commercialProposal', 'clinic']);

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('representative_id')) {
            $query->where('representative_id', $request->representative_id);
        }

        $codes = $query->latest()->paginate(20);

        // Indicators
        $totalCodes = ReferralCode::count();
        $usedCodes = ReferralCode::where('status', ReferralCode::STATUS_UTILIZADO)->count();
        $expiredCodes = ReferralCode::where('status', ReferralCode::STATUS_EXPIRADO)->count();
        $conversionRate = $totalCodes > 0 ? round(($usedCodes / $totalCodes) * 100, 2) : 0;

        return view('admin.referral_codes.index', compact(
            'codes', 
            'totalCodes', 
            'usedCodes', 
            'expiredCodes', 
            'conversionRate'
        ));
    }

    public function show(ReferralCode $referralCode)
    {
        $referralCode->load(['representative', 'commercialProposal', 'clinic']);
        return view('admin.referral_codes.show', compact('referralCode'));
    }
}
