<?php

namespace App\Services;

use App\Models\MonthShopData;
use App\Models\Schedule;
use App\Models\Worker;

class ScheduleService
{
    public function getSchedule(string $bxShopId, \DateTime $date)
    {
        $schedule = Schedule::where('shop_bx_id', '=', $bxShopId)
            ->whereMonth('date', '=', $date->format('m'))
            ->whereYear('date', '=', $date->format('Y'))
            ->with('user')
            ->with('shop_role')
            ->with('shop')
            ->get();
        $prepared = [];
        $workersPerDay = [];
        foreach ($schedule as $row) {
            $date = new \DateTime($row->date);
            $day = (int)$date->format('d');
            if ($row->hours > 0 && $row->shop_role->count_in_day) {
                if (!empty($workersPerDay[$day])) {
                    $workersPerDay[$day]++;
                } else {
                    $workersPerDay[$day] = 1;
                }

            }
            if (empty($prepared[$row->shop_role->title]['workers'][$row->user->bx_id])) {
                $prepared[$row->shop_role->title]['role'] = $row->shop_role;
                $nameExploded = explode(' ', $row->user->name);
                $name = $nameExploded[0] . ' ' .
                    mb_substr($nameExploded[1], 0, 1) . '.' .
                    mb_substr($nameExploded[1], 0, 1). '.';
                $prepared[$row->shop_role->title]['workers'][$row->user->bx_id] = [
                    'id' => $row->user->bx_id,
                    'name' => $name,
                    'role' => $row->shop_role->name,
                    'schedule' => [$day => $row->hours],
                    'sum_hours' => $row->hours
                ];
            } else {
                $prepared[$row->shop_role->title]['workers'][$row->user->bx_id]['schedule'][$day] = $row->hours;
                $prepared[$row->shop_role->title]['workers'][$row->user->bx_id]['sum_hours'] += $row->hours;
            }
        }
        usort($prepared, function ($item1, $item2) {
            $direction = [
                'not_found' => 10,
                'seller' => 20,
                'cashier_seller' => 30,
                'cashier' => 40,
                'manager' => 50,
                'admin' => 60,
                'storekeeper' => 70,
                'merchandiser' => 80,
            ];
            return $direction[$item1['role']->title] <=> $direction[$item2['role']->title];
        });
        return [
            'table' => $prepared,
            'workers_per_day' => $workersPerDay
        ];
    }

    public function editWorker(string $shopId, string $workerId, int $roleId, array $schedule, \DateTime $month)
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month->format('m'), $month->format('Y'));
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateString = $month->format('y') . '-' . $month->format('m') . '-' .  $i;
            $prepared[] = [
                'shop_bx_id' => $shopId,
                'worker_id' => $workerId,
                'shop_role_id' => $roleId,
                'date' => $dateString,
                'hours' => $schedule[$i] ?? 0
            ];
        }
        return Worker::upsert($prepared, ['shop_bx_id', 'worker_bx_id', 'shop_role_id', 'date']);
    }

    public function getMonthData(string $shopId, \DateTime $month)
    {
        $monthData = MonthShopData::where('shop_bx_id', '=', $shopId)
            ->whereMonth('month', '=', $month->format('m'))
            ->whereYear('month', '=', $month->format('Y'))
            ->first();
        if (!$monthData) {
            $monthData = new MonthShopData([
                'bx_shop_id' => $shopId,
                'month' => $month->format('Y-m-d'),
                'comments' => '',
                'sales_plan' => 0,
                'qty_workers' => 0,
                'month_closed' => false,
                'created_at' => (new \DateTime())->format('Y-m-d'),
                'updated_at' => (new \DateTime())->format('Y-m-d')
            ]);
            $monthData->save();
        }
        return $monthData;
    }

    public function editMonthData(string $shopId, \DateTime $month, array $monthData)
    {
        $monthData = MonthShopData::where('shop_bx_id', '=', $shopId)
            ->whereMonth('month', '=', $month->format('m'))
            ->whereYear('month', '=', $month->format('Y'))
            ->first();
        if (!$monthData) {
            $monthData = new MonthShopData([
                'shop_bx_id' => $shopId,
                'month' => $month->format('Y-m-d'),
                'comments' => '',
                'sales_plan' => 0,
                'qty_workers' => 0,
                'month_closed' => false,
                'created_at' => (new \DateTime())->format('Y-m-d'),
                'updated_at' => (new \DateTime())->format('Y-m-d')
            ]);
            $monthData->save();
        }
        $monthData->comments = $monthData['comments'];
        $monthData->sales_plan = $monthData['sales_plan'];
        $monthData->qty_workers = $monthData['qty_workers'];
        $monthData->month_closed = $monthData['month_closed'];
        $monthData->save();
        return $monthData;
    }
}