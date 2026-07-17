<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuPermissionAuditLog extends Model
{
    use BelongsToCompany;
    protected $table = 'menu_permission_audit_logs';

    protected $fillable = [
        'user_id',
        'role_id',
        'academy_company_id',
        'action',
        'payload',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, MenuPermissionAuditLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * @deprecated Use role() — coluna correta é role_id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Role, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->role();
    }
}
