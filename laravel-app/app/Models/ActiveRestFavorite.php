<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveRestFavorite extends Model
{
    protected $fillable = ['user_id', 'active_rest_routine_id'];

    public function routine()
    {
        return $this->belongsTo(ActiveRestRoutine::class, 'active_rest_routine_id');
    }
}
