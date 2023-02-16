<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopRole extends Model
{
    public $timestamps = false;

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'role_id', 'role_id');
    }
}
