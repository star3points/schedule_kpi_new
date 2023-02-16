<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Schedule;
use App\Models\Shop;
use App\Models\Worker;
use App\Services\ExternalApi\OneC;
use Illuminate\Database\Eloquent\Collection as DBCollection;

class SalesUpdater
{
    protected DBCollection $workerList;
    protected DBCollection $shopList;
    protected DBCollection $schedule;
    public function __construct()
    {
        $this->shopList = Shop::all();
        $this->workerList = Worker::all();
    }

    public function update(\DateTime $dateFrom, \DateTime $dateTo)
    {
        $this->schedule = Schedule::whereBetween('date', [
            $dateFrom->format('Y-m-d'),
            $dateFrom->format('Y-m-d')
        ])->get();
        $sales = OneC::getSales($dateFrom, $dateTo);
        $sales = collect($sales);
        $sales = $sales->filter(function ($sale) {
            return !(
                (str_contains($sale['Склад'], 'Склад')) ||
                (str_contains($sale['Склад'], 'Интернет')) ||
                (str_contains($sale['Продавец'], 'Реклама')) ||
                (str_contains($sale['Продавец'], 'Касса')) ||
                (str_contains($sale['Продавец'], 'Admin')) ||
                (empty($sale['Продавец'])) ||
                (empty($sale['Склад']))
            );
        });
        $sales = $sales->map(function ($sale) {
            $date = date_create(substr($sale['День'], 0, 10));
            $shopBxId = $this->getShopId(str_replace("  ", " ", trim($sale['Склад'])));
            $userBxId = $this->getWorkerId($sale['Продавец']);
            $shopRole = $this->getShopRole(date: $date, shopId: $shopBxId, userId: $userBxId);
            return [
                'date' => $date->format('Y-m-d'),
                'shop_bx_id' => $shopBxId,
                'user_bx_id' => $userBxId,
                'shop_role' => $shopRole,
                'sales_checks' => ($sale['Чеки'] - $sale['ДоковВозврат']) ?: 0,
                'sales_products' => $sale['ТоваровПродажа'] ?: 0,
                'sales_sum' => round((float)($sale['Сумма']), 2),
                'return_checks' => (int)$sale['ДоковВозврат'],
                'return_products' => (int)$sale['ТоваровВозврат'],
                'return_sum' => round(
                    ($sale['ВозвратБезналичные'] +
                        $sale['ВозвратНаличными'] +
                        $sale['ВозвратБезналичнымиНеДеньВДень'] +
                        $sale['ВозвратНаличнымиНеДеньВДень']) ?:
                        0
                ),
            ];
        });
        return Sale::upsert(
            $sales->toArray(),
            ['date', 'shop_bx_id', 'user_bx_id', 'shop_role']
        );
    }

    protected function getWorkerId(string $oneCWorkerName)
    {
        $workerModel = $this->workerList
            ->where('name', '=', $oneCWorkerName)
            ->first();
        if (!$workerModel) {
            $oneCWorkerName = str_replace('  ', ' ', $oneCWorkerName);
            $lastName = trim(explode(' ', $oneCWorkerName)[0]);
            $firstName = trim(explode(' ', $oneCWorkerName)[1] ?? dd($oneCWorkerName));
            $workerModel = $this->workerList
                ->filter(function ($user) use ($firstName, $lastName) {
                    return (
                        str_contains($user->name, $lastName) &&
                        str_contains($user->name, $firstName)
                    );
                })
                ->first();
        }
        return $workerModel ? $workerModel->bx_id : 0;
    }

    protected function getShopId(string $shopName)
    {
        $shopModel = $this->shopList
            ->where('name', '=', $shopName)
            ->first();
        return $shopModel ? $shopModel->bx_id : 0;
    }

    protected function getShopRole(\DateTime $date, int $shopId, int $userId): int
    {
        $schedule = $this->schedule->where('user_bx_id', '=', $userId)
            ->where('shop_bx_id', '=', $shopId)
            ->where('date', '=', $date->format('Y-m-d'))
            ->first();
        if ($schedule) {
            return $schedule->role_id;
        } else {
            $schedule = Schedule::where('user_bx_id', '=', $userId)
                ->where('shop_bx_id', '=', $shopId)
                ->latest('date')
                ->first();
            return $schedule ? $schedule->role_id : 0;
        }
    }
}