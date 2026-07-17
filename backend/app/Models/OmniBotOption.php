<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmniBotOption extends Model
{
    protected $fillable = ['step_id', 'trigger_value', 'label', 'destination_step_id'];
}
