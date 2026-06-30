<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopSupplier extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'academy_company_id',
        'name',
        'document',
        'contact_name',
        'email',
        'phone',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'address' => 'array',
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(ShopProduct::class, 'supplier_id');
    }
}
