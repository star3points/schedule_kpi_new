<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected Seeder $calendar;
    protected Seeder $shopRole;
//    protected Seeder $shop;
    public function __construct()
    {
        $this->calendar = new OldCalendarSeeder();
        $this->monthShopData = new MonthShopDataSeeder();
        $this->shopRole = new ShopRoleSeeder();
//        $this->shop = new ShopSeeder();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $this->shop->run();
        $this->shopRole->run();
        $this->calendar->run();
        $this->monthShopData->run();
        // update sales
    }
}
