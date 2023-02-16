<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthShopData extends Model
{
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_bx_id', 'bx_id');
    }
}
