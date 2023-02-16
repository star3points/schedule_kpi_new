<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public $timestamps = false;

    public function shop_role()
    {
        return $this->belongsTo(ShopRole::class, 'role_id', 'role_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_bx_id', 'bx_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_bx_id', 'bx_id');
    }
}
