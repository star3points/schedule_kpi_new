<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

class OldCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $calendar
        require 'calendar_dump.php';

        $shops = Shop::all();
        $preparedSchedule = [];
        $preparedUsers = [];
        foreach ($calendar as $row) {
            $shop = $shops->where('ts_id', '=', $row['shopid'])->first();
            $preparedUsers[$row['user_id']] = [
                'bx_id' => $row['user_id'],
                'name' => $row['name']
            ];
            foreach ($row as $key => $value) {
                if (is_numeric($key)) {
                    $preparedSchedule[] = [
                        'date' => (new \DateTime($row['year'] . '-' . $row['mounth'] . '-' . $key))->format('Y-m-d'),
                        'hours' => $value ?: 0,
                        'user_bx_id' => $row['user_id'],
                        'shop_bx_id' => $shop ? $shop->bx_id : 0,
                        'shop_role_id' => $row['jobid']
                    ];
                }
            }
        }
        User::upsert($preparedUsers, ['bx_id'], ['name']);
        $step = 500;
        for ($i = 0; $i < count($preparedSchedule); $i+=$step) {
            $prepared = array_slice($preparedSchedule, $i, $step);
            Schedule::upsert($prepared, ['user_bx_id', 'shop_bx_id', 'role_id', 'date'], ['hours']);
        }
    }
}
