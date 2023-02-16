<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MonthShopDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $monthData = include 'comment_dump.php';
        $prepared = [];
        foreach ($monthData as $data) {
            $prepared[] = [

            ];
        }
    }
}
