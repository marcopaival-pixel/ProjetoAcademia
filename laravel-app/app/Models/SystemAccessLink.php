<?php

namespace App\Models;

use App\Models\Traits\FillsTenantColumns;
use App\Models\Traits\HasClinic;
use Illuminate\Database\Eloquent\Model;

class SystemAccessLink extends Model
{
    use FillsTenantColumns;
    use HasClinic;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'academy_company_id',
        'system_name',
        'system_url',
        'qr_code_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
