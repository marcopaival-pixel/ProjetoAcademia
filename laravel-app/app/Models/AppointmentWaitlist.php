<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentWaitlist extends Model
{
    protected $fillable = [
        'patient_id',
        'professional_id',
        'requested_date',
        'status',
    ];

    protected $casts = [
        'requested_date' => 'datetime',
    ];
}
