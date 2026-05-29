<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminField extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_entity_id',
        'name',
        'label',
        'type',
        'is_required',
        'is_readonly',
        'is_searchable',
        'is_filterable',
        'is_sortable',
        'is_visible_list',
        'is_visible_form',
        'default_value',
        'placeholder',
        'help_text',
        'validation_rules',
        'options',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_readonly' => 'boolean',
        'is_searchable' => 'boolean',
        'is_filterable' => 'boolean',
        'is_sortable' => 'boolean',
        'is_visible_list' => 'boolean',
        'is_visible_form' => 'boolean',
        'options' => 'array',
        'sort_order' => 'integer',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(AdminEntity::class, 'admin_entity_id');
    }
}
