<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Services\ExternalApi\OneC;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shops = OneC::getShopList();
        foreach ($shops as $shop) {
            if (!empty($shop['bitrix_id'])) {
                $prepared[] = [
                    'name' => $shop['Наименование'],
                    'bx_id' => $shop['bitrix_id'],
                    'ts_id' =>  preg_replace("/\D/", '', $shop['ИдТС'] ?? '')
                ];
            }
        }
        Shop::upsert($prepared, ['bx_id'], ['name', 'ts_id']);
    }
}
