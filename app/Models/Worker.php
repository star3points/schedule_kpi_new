<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'bx_id', 'worker_bx_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'bx_id', 'worker_bx_id');
    }
}
