<?php

namespace App\Services\TableConstructor;

use Illuminate\Support\Collection;
use stdClass;

class AntdTableConstructor implements TableConstructorInterface
{
    /***
     * @param Collection<StdClass> $shopsWithSales
     * @return array
     */
    public function prepareShops(Collection $shopsWithSales)
    {
        $shop = $shopsWithSales->first();
        $columnsMap = [
            'name' => 'Магазин',
            'sales_sum' => 'Продажи',
            'fact_percent' => 'Факт',
            'forecast_percent' => 'Прогноз',
            'checks' => 'Чеки',
            'products' => 'Товары',
            'return_sum' => 'Сумма возвратов',
            'return_checks' => 'Чеков возврат',
            'return_products' => 'Товаров возврат',
        ];
        $columns = [];
        foreach (array_keys(get_object_vars($shop)) as $key) {
            $columnTitle = $columnsMap[$key] ?? '';
            if ($columnTitle) {
                $columns[] = [
                    'title' => $columnTitle,
                    'name' => $key,
                    'dataIndex' => $key
                ];
            }
        }
        $prepared = [
            'columns' => $columns,
            'table' => $shopsWithSales
        ];
        return $prepared;
    }

    //TODO: add calculate
    public function prepareShop(Collection $shopWithSales)
    {
        $columnsMap = [
            'name' => 'Работник',
            'shop_role' => 'Роль',
            'sales_sum' => 'Продажи',
            'fact_percent' => 'Факт',
            'forecast_percent' => 'Прогноз',
            'checks' => 'Чеки',
            'products' => 'Товары',
            'return_sum' => 'Сумма возвратов',
            'return_checks' => 'Чеков возврат',
            'return_products' => 'Товаров возврат',
        ];
        foreach (get_object_vars($shopWithSales) as $key) {
            $columnTitle = $columnsMap[$key] ?? '';
            if ($columnTitle) {
                $columns[] = [
                    'title' => $columnTitle,
                    'name' => $key,
                    'dataIndex' => $key
                ];
            }
        }
        $prepared = [
            'columns' => $columns,
            'table' => $shopWithSales
        ];
        return $prepared;
    }
}