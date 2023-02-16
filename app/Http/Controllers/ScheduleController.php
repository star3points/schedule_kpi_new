<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopRole;
use App\Services\ExternalApi\Bitrix\BitrixUserInterface;
use App\Services\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleService $scheduleService,
        protected BitrixUserInterface $user
    ){ }

    public function getRole()
    {
        return ['role' => $this->user->getRole()];
    }
    public function getShopList()
    {
        return Shop::all();
    }

    public function getShopRoleList()
    {
        return ShopRole::all();
    }

    public function getSchedule(Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'exists:App\Models\Shop,bx_id',
            'month' => 'required|date'
        ]);
        return $this->scheduleService->getSchedule(
            $request->get('shop_id') ?? $this->user->getDepartment(),
            new \DateTime($request->get('month'))
        );
    }

    public function editWorker(Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'required|exists:App\Models\Shop,bx_id',
            'worker_id' => 'required',
            'role_id' => 'required|exists:App\Models\ShopRole,role_id',
            'month' => 'required',
            'schedule' => 'required|array'
        ]);
        return $this->scheduleService->editWorker(
            shopId: $request->get('shop_id'),
            workerId: $request->get('worker_id'),
            roleId: $request->get('role_id'),
            schedule: $request->get('schedule'),
            month: new \DateTime($request->get('month'))
        );
    }

    public function getMonthData(Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'required|exists:App\Models\Shop,bx_id',
            'month' => 'required|date'
        ]);
        return $this->scheduleService->getMonthData(
            shopId: $request->get('shop_id'),
            month: new \DateTime($request->get('month'))
        );
    }

    public function editMonthData(Request $request)
    {
        $this->validate($request, [
            'shop_id' => 'required|exists:App\Models\Shop,bx_id',
            'month' => 'required|date',
            'month_data' => 'required|array'
        ]);
        return $this->scheduleService->editMonthData(
            shopId: $request->get('shop_id'),
            month: new \DateTime($request->get('month')),
            monthData: $request->get('month_data')
        );
    }
}
