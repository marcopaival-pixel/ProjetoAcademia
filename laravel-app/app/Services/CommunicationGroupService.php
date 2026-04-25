<?php

namespace App\Services;

use App\Models\CommunicationGroup;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommunicationGroupService
{
    public function createGroup(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = Str::slug($data['name']);
            $data['is_private'] = isset($data['is_private']);
            $data['allow_self_join'] = isset($data['allow_self_join']);
            $data['is_active'] = isset($data['is_active']);
            $data['can_members_send_messages'] = isset($data['can_members_send_messages']);

            $group = CommunicationGroup::create($data);

            $this->logAction('create_group', $group, $data);

            return $group;
        });
    }

    public function updateGroup(CommunicationGroup $group, array $data)
    {
        return DB::transaction(function () use ($group, $data) {
            $data['slug'] = Str::slug($data['name']);
            $data['is_private'] = isset($data['is_private']);
            $data['allow_self_join'] = isset($data['allow_self_join']);
            $data['is_active'] = isset($data['is_active']);
            $data['can_members_send_messages'] = isset($data['can_members_send_messages']);

            $group->update($data);

            $this->logAction('update_group', $group, $data);

            return $group;
        });
    }

    public function deleteGroup(CommunicationGroup $group)
    {
        return DB::transaction(function () use ($group) {
            $this->logAction('delete_group', $group, $group->toArray());
            return $group->delete();
        });
    }

    public function approveRequest($requestId)
    {
        return DB::table('communication_group_user')
            ->where('id', $requestId)
            ->update([
                'status' => 'approved',
                'updated_at' => now(),
            ]);
    }

    public function rejectRequest($requestId)
    {
        return DB::table('communication_group_user')
            ->where('id', $requestId)
            ->update([
                'status' => 'rejected',
                'updated_at' => now(),
            ]);
    }

    public function setUserRole(CommunicationGroup $group, $userId, $role)
    {
        return $group->users()->updateExistingPivot($userId, [
            'role' => $role,
            'updated_at' => now(),
        ]);
    }

    public function removeMember(CommunicationGroup $group, $userId)
    {
        return $group->users()->detach($userId);
    }

    private function logAction(string $action, $model, array $payload)
    {
        AdminLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => [
                'model_id' => $model->id,
                'model_type' => get_class($model),
                'data' => $payload,
            ],
            'created_at' => now(),
        ]);
    }
}
