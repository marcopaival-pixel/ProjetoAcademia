<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CommunicationGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_private',
        'allow_self_join',
        'is_active',
        'can_members_send_messages',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'allow_self_join' => 'boolean',
        'is_active' => 'boolean',
        'can_members_send_messages' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'communication_group_user', 'group_id', 'user_id')
            ->withPivot('id', 'status', 'role')
            ->withTimestamps();
    }

    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('status', 'approved');
    }

    public function pendingMembers(): BelongsToMany
    {
        return $this->users()->wherePivot('status', 'pending');
    }

    public function moderators(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('status', 'approved')
            ->wherePivot('role', 'moderator');
    }
}
