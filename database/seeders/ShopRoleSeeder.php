<?php

namespace Database\Seeders;

use App\Models\ShopRole;
use Illuminate\Database\Seeder;

class ShopRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            0 => ['Не найдено', 'not_found', 0],
            1 => ['Продавец-консультант', 'seller', 1],
            2 => ['Продавец-кассир', 'cashier_seller', 1],
            7 => ['Кассир', 'cashier', 1],
            3 => ['Менеджер', 'manager', 0],
            4 => ['Администратор', 'admin', 0],
            5 => ['Кладовщик', 'storekeeper', 0],
            6 => ['Мерчендайзер', 'merchandiser', 0],
        ];
        foreach ($roles as $key => $role) {
            $prepared[] = [
                'role_id' => $key,
                'title' => $role[1],
                'name' => $role[0],
                'count_in_day' => $role[2]
            ];
        }
        ShopRole::upsert($prepared, ['role_id'], ['title', 'name', 'count_in_day']);
    }
}
