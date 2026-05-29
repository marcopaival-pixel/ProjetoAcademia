<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPermission extends Model
{
    protected $table = 'plan_permissions';

    protected $fillable = [
        'plan_id',
        'permission_id',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
