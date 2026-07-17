<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'label',
        'route',
        'match_mode',
        'icon',
        'order',
        'is_required',
        'parent_id',
        'portal',
        'is_container',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_container' => 'boolean',
    ];

    public function preferences(): HasMany
    {
        return $this->hasMany(UserMenuPreference::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Menu, Menu>
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Menu, Menu>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<RoleMenuPermission, Menu>
     */
    public function roleMenuPermissions(): HasMany
    {
        return $this->hasMany(RoleMenuPermission::class, 'menu_id');
    }
}
