<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationGroup;
use App\Models\User;
use App\Services\CommunicationGroupService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GroupAdminController extends Controller
{
    protected $groupService;

    public function __construct(CommunicationGroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function index(): View
    {
        $groups = CommunicationGroup::withCount('members')->get();
        return view('admin.groups.index', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_private' => 'sometimes',
            'allow_self_join' => 'sometimes',
            'is_active' => 'sometimes',
            'can_members_send_messages' => 'sometimes',
        ]);

        $this->groupService->createGroup($data);

        return back()->with('success', 'Grupo criado com sucesso.');
    }

    public function edit(CommunicationGroup $group): View
    {
        return view('admin.groups.edit', compact('group'));
    }

    public function update(Request $request, CommunicationGroup $group): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_private' => 'sometimes',
            'allow_self_join' => 'sometimes',
            'is_active' => 'sometimes',
            'can_members_send_messages' => 'sometimes',
        ]);

        $this->groupService->updateGroup($group, $data);

        return redirect()->route('admin.groups.index')->with('success', 'Grupo atualizado.');
    }

    public function destroy(CommunicationGroup $group): RedirectResponse
    {
        $this->groupService->deleteGroup($group);
        return back()->with('success', 'Grupo excluído.');
    }

    public function members(CommunicationGroup $group): View
    {
        $members = $group->users()
            ->withPivot('id', 'status', 'role')
            ->orderBy('communication_group_user.created_at', 'desc')
            ->get();

        return view('admin.groups.members', compact('group', 'members'));
    }

    public function updateMemberRole(Request $request, CommunicationGroup $group, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:member,moderator,admin'
        ]);

        $this->groupService->setUserRole($group, $user->id, $validated['role']);

        return back()->with('success', 'Permissão do membro atualizada.');
    }

    public function removeMember(CommunicationGroup $group, User $user): RedirectResponse
    {
        $this->groupService->removeMember($group, $user->id);
        return back()->with('success', 'Membro removido do grupo.');
    }

    public function requests(): View
    {
        $requests = \Illuminate\Support\Facades\DB::table('communication_group_user')
            ->join('users', 'communication_group_user.user_id', '=', 'users.id')
            ->join('communication_groups', 'communication_group_user.group_id', '=', 'communication_groups.id')
            ->where('communication_group_user.status', 'pending')
            ->select(
                'communication_group_user.id',
                'users.name as user_name',
                'users.email as user_email',
                'communication_groups.name as group_name',
                'communication_group_user.created_at'
            )
            ->get();

        return view('admin.groups.requests', compact('requests'));
    }

    public function approveRequest($id): RedirectResponse
    {
        $this->groupService->approveRequest($id);
        return back()->with('success', 'Solicitação aprovada.');
    }

    public function rejectRequest($id): RedirectResponse
    {
        $this->groupService->rejectRequest($id);
        return back()->with('success', 'Solicitação rejeitada.');
    }
}
