<?php

namespace App\Http\Controllers;

use App\Models\CommunicationGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CommunicationGroupController extends Controller
{
    public function index(): View
    {
        $groups = CommunicationGroup::where('is_active', true)->withCount('members')->get();
        $myGroupIds = Auth::user()->communicationGroups()->pluck('communication_groups.id')->toArray();
        $pendingGroupIds = Auth::user()->communicationGroups()->wherePivot('status', 'pending')->pluck('communication_groups.id')->toArray();
        $approvedGroupIds = Auth::user()->communicationGroups()->wherePivot('status', 'approved')->pluck('communication_groups.id')->toArray();

        return view('groups.index', compact('groups', 'myGroupIds', 'pendingGroupIds', 'approvedGroupIds'));
    }

    public function join(CommunicationGroup $group): RedirectResponse
    {
        if (!$group->is_active) {
            return back()->with('error', 'Este grupo está desativado no momento.');
        }

        $user = Auth::user();

        if ($user->communicationGroups()->where('group_id', $group->id)->exists()) {
            return back()->with('error', 'Você já solicitou entrada ou já faz parte deste grupo.');
        }

        $status = $group->is_private ? 'pending' : 'approved';
        
        $user->communicationGroups()->attach($group->id, [
            'status' => $status,
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $msg = $status === 'pending' ? 'Solicitação de entrada enviada. Aguarde aprovação!' : 'Você entrou no grupo!';
        
        return back()->with('success', $msg);
    }

    public function leave(CommunicationGroup $group): RedirectResponse
    {
        Auth::user()->communicationGroups()->detach($group->id);
        return back()->with('success', 'Você saiu do grupo.');
    }
}
