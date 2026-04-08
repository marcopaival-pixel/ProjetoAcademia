<?php

namespace App\Http\Controllers;

use App\Models\BodyAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(): View
    {
        $assessments = BodyAssessment::where('user_id', Auth::id())
            ->orderBy('assessment_date', 'desc')
            ->get();

        return view('assessments.index', compact('assessments'));
    }

    public function create(): View
    {
        return view('assessments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'assessment_date' => 'required|date',
            'weight_kg' => 'nullable|numeric|min:20|max:500',
            'bf_percent' => 'nullable|numeric|min:1|max:70',
            'muscle_percent' => 'nullable|numeric|min:1|max:90',
            'neck' => 'nullable|numeric',
            'chest' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'abdomen' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
            'bicep_l' => 'nullable|numeric',
            'bicep_r' => 'nullable|numeric',
            'forearm_l' => 'nullable|numeric',
            'forearm_r' => 'nullable|numeric',
            'thigh_l' => 'nullable|numeric',
            'thigh_r' => 'nullable|numeric',
            'calf_l' => 'nullable|numeric',
            'calf_r' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();

        BodyAssessment::create($data);

        return redirect()->route('assessments.index')->with('success', 'Avaliação física registrada com sucesso!');
    }

    public function show(BodyAssessment $assessment): View
    {
        if ($assessment->user_id !== Auth::id()) abort(403);
        return view('assessments.show', compact('assessment'));
    }

    public function destroy(BodyAssessment $assessment)
    {
        if ($assessment->user_id !== Auth::id()) abort(403);
        $assessment->delete();
        return back()->with('success', 'Avaliação removida.');
    }
}
