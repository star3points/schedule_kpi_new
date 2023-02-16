<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public $timestamps = false;
    public $guarded = [];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'shop_bx_id', 'bx_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'shop_bx_id', 'bx_id');
    }

    public function month_shop_data()
    {
        return $this->hasMany(MonthShopData::class, 'shop_bx_id', 'bx_id');
    }

}
