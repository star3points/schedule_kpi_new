<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Models\Shop;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Collection as DbCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KpiRepository
{
    public function getShops(\DateTime $dateFrom, \DateTime $dateTo, bool $toStd = true): Collection
    {
        $salesQuery = Sale::query()
            ->addSelect('shop_bx_id')
            ->addSelect(DB::raw('sum(sales_sum) as sales_sum'))
            ->addSelect(DB::raw('sum(sales_checks) as sales_checks'))
            ->addSelect(DB::raw('sum(sales_products) as sales_products'))
            ->addSelect(DB::raw('sum(return_sum) as return_sum'))
            ->addSelect(DB::raw('sum(return_checks) as return_checks'))
            ->addSelect(DB::raw('sum(return_products) as return_products'))
            ->whereBetween('date', [
                $dateFrom->format('Y-m-d'),
                $dateTo->format('Y-m-d')
            ])->groupBy('shop_bx_id');
        $shopsWithSales = Shop::with(['month_shop_data' => function ($q) use ($dateFrom) {
            $q->whereMonth('date', '=', $dateFrom->format('m'))
                ->whereYear('date', '=', $dateFrom->format('Y'));
        }])->leftJoinSub(
            $salesQuery, 'sales',
            'sales.shop_bx_id', '=', 'shops.bx_id'
        )->get();
        return $toStd ? $this->toStdClassCollection($shopsWithSales) : $shopsWithSales;
    }

    public function getShop(
        \DateTime $dateFrom, \DateTime $dateTo,
        string $shopBxId,
        bool $toStd = true
    ): Collection|DbCollection {
        $salesQuery = Sale::query()
            ->addSelect('user_bx_id')
            ->addSelect(DB::raw('sum(sum) as sum'))
            ->addSelect(DB::raw('sum(checks) as checks'))
            ->addSelect(DB::raw('sum(products) as products'))
            ->addSelect(DB::raw('sum(return_sum) as return_sum'))
            ->addSelect(DB::raw('sum(return_checks) as return_checks'))
            ->addSelect(DB::raw('sum(return_products) as return_products'))
            ->where('shop_bx_id', '=', $shopBxId)
            ->whereBetween('date', [
                $dateFrom->format('Y-m-d'),
                $dateTo->format('Y-m-d')
            ])->groupBy('user_bx_id');
        $usersSales = Worker::whereHas('sales', function ($q) use ($shopBxId) {
            $q->where('shop_bx_id', '=', $shopBxId);
        })->leftJoinSub($salesQuery, 'sales',
            'sales.user_bx_id', '=', 'user.bx_id'
        )->get();
        return $toStd ? $this->toStdClassCollection($usersSales) : $usersSales;
    }

    public function getWorker(
        \DateTime $dateFrom, \DateTime $dateTo,
        string $shopBxId, string $workerBxId,
        bool $toStd = true
    ): Collection {
        $salesQuery = Sale::query()
            ->addSelect('user_bx_id')
            ->addSelect(DB::raw('sum(sum) as sum'))
            ->addSelect(DB::raw('sum(checks) as checks'))
            ->addSelect(DB::raw('sum(products) as products'))
            ->addSelect(DB::raw('sum(return_sum) as return_sum'))
            ->addSelect(DB::raw('sum(return_checks) as return_checks'))
            ->addSelect(DB::raw('sum(return_products) as return_products'))
            ->where('shop_bx_id', '=', $shopBxId)
            ->where('user_bx_id', '=', $workerBxId)
            ->whereBetween('date', [
                $dateFrom->format('Y-m-d'),
                $dateTo->format('Y-m-d')
            ])->groupBy('user_bx_id');
        $workerSales = Worker::where('bx_id', '=', $workerBxId)
            ->leftJoinSub($salesQuery, 'sales',
                'sales.user_bx_id', '=', 'user.bx_id'
            )->get();
        return $toStd ? $this->toStdClassCollection($workerSales) : $workerSales;
    }

    protected function toStdClassCollection(DbCollection $collection): Collection
    {
        $result = collect();
        $collection->map(function ($item) use ($result) {
            $result->push((object)($item->toArray()));
        });
//        dd(get_class($collection->first()), get_class($result->first()));
        return $result;
    }
}