<?php

namespace App\Http\Controllers;

use App\Services\ExternalApi\Bitrix\BitrixUserInterface;
use App\Services\KpiService;
use App\Services\SalesUpdater;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function __construct(
        protected KpiService $kpiService,
        protected BitrixUserInterface $user
    ) {}

    public function getRole()
    {
        return ['role' => $this->user->getRole()];
    }

    public function updateSales(Request $request)
    {
        $this->validate($request, [
            'date_from' => 'required|date',
            'date_to' => 'required|date'
        ]);
        return (new SalesUpdater())->update(
            dateFrom: new \DateTime($request->get('date_from')),
            dateTo: new \DateTime($request->get('date_to'))
        );
    }

    public function getShops(Request $request)
    {
        $this->validate($request, [
            'date_from' => 'required|date',
            'date_to' => 'required|date'
        ]);
        return $this->kpiService->getShops(
            dateFrom: new \DateTime($request->get('date_from')),
            dateTo: new \DateTime($request->get('date_to'))
        );
    }

    public function getShop(Request $request)
    {
        $this->validate($request, [
            'date_from' => 'required|date',
            'date_to' => 'required|date'
        ]);
        return $this->kpiService->getShop(
            dateFrom: new \DateTime($request->get('date_from')),
            dateTo: new \DateTime($request->get('date_to')),
            shopBxId: $request->get('shop_id') ?? $this->user->getDepartment()
        );
    }

    public function getWorker(Request $request)
    {
        $this->validate($request, [
            'date_from' => 'required|date',
            'date_to' => 'required|date'
        ]);
        if (
            ($this->user->getRole() == 'Admin' || $this->user->getRole() == 'Manager')
            && $request->get('worker_id')
            && $request->get('shop_id')
        ) {
            return $this->kpiService->getWorker(
                dateFrom: new \DateTime($request->get('date_from')),
                dateTo: new \DateTime($request->get('date_to')),
                shopBxId: $request->get('shop_id'),
                workerBxId: $request->get('worker_id')
            );
        } elseif ($this->user->getRole() == 'Worker') {
            return $this->kpiService->getWorker(
                dateFrom: new \DateTime($request->date_from),
                dateTo: new \DateTime($request->date_to),
                shopBxId: $this->user->getDepartment(),
                workerBxId: $this->user->getId()
            );
        }
        return [];

    }
}
