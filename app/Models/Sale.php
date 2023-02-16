<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_bx_id', 'bx_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_bx_id', 'bx_id');
    }

    public function shop_role()
    {
        return $this->belongsTo(ShopRole::class, 'shop_role_id', 'id');
    }
}
