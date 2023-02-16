<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Shop;
use App\Models\Worker;
use App\Repositories\KpiRepository;
use App\Services\TableConstructor\TableConstructorInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class KpiService
{

    public function __construct(
        private KpiRepository $kpiRepository,
        private TableConstructorInterface $tableConstructor
    ) {}

    public function updateSales(\DateTime $sDateFrom, \DateTime $sDateTo)
    {
        $salesUpdater = new SalesUpdater();
        $salesUpdater->update($sDateFrom, $sDateTo);
    }

    public function getShops(\DateTime $dateFrom, \DateTime $dateTo)
    {
        $shops = $this->kpiRepository->getShops($dateFrom, $dateTo);
        $shops->map(function ($shop) use ($dateFrom, $dateTo) {
            $salesPlan = $shop->month_shop_data[0]->sales_plan ?? 0;
            $shop->sales_plan = $salesPlan;
            $shop->fact_percent = !$salesPlan ? 0 :
                round($shop->sales_sum / $shop->sales_plan * 100, 1) . '%';
            $shop->forecast_pesent = !$salesPlan ? 0 :
                round($shop->sales_sum / $salesPlan * 100
                / ((int)$dateTo->format('d') - (int)$dateFrom->format('d') + 1) * (int)$dateFrom->format('t'),
                1);

        });
        $table = $this->tableConstructor->prepareShops($shops);
        return $table;
    }

    public function getShop(\DateTime $dateFrom, \DateTime $dateTo, string $shopBxId)
    {
        $shopSales = $this->kpiRepository->getShop($dateFrom, $dateTo, $shopBxId);
        $table = $this->tableConstructor->prepareShop($shopSales);
        return $table;
    }

    public function getWorker(\DateTime $dateFrom, \DateTime $dateTo, string $shopBxId, string $workerBxId)
    {
        $workerSales = $this->kpiRepository->getWorker(
            dateFrom: $dateFrom, dateTo: $dateTo,
            shopBxId: $shopBxId,
            workerBxId: $workerBxId
        );
        return $workerSales;
    }
}