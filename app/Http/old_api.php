<?php


const DEV_MODE = 1;
if (DEV_MODE) {
    header('Access-Control-Allow-Origin: *');
}

use kpi\ApiNew as ApiNew;

class api_kpi extends ApiApp
{

    private $role;
    private $access;
    private const AUDITORS_RU = [7201, 32133, 93362, 6127, 33306, 139631, 112554, 153816];
    private const AUDITORS_KZ = [];
    private const AUDITORS_OMSK_NSB = [];
    private const JOB_ID_POSITION = [
        0 => '',
        1 => 'Продавец-консультант',
        2 => 'Продавец-кассир',
        7 => 'Касир',
        3 => 'Менеджер',
        4 => 'Администратор',
        5 => 'Кладовщик',
        6 => 'Мерчендайзер'
    ];
    /*
     * Вход
        Галерея Спб
        Атриум
        Ростокино
        Казань
        Нижний Новгород
        Ростов
        Екатеринбург

     * Выход
        Сочи
        Краснодар
        Европолис
        Афимолл
        Коламбус
        Метрополис

     */
    private const COUNT_VISITORS_RULE = [
        'entered' => [
            28795,
            153682,
            21786,
            99998,
            99999,
            165954,
            64295
        ],
        'went_out' => [
            76083,
            135027,
            21786, 21788,
            24375,
            44371,
            28792
        ]
    ];

    /**
     * @param $sMethod
     */
    public function init($sMethod)
    {
        if ($sMethod == 'cronupdatesalesdata') {
            $arResult = $this->call_method('ev_' . $sMethod);
            $this->jsonAnswer($arResult);
            return;
        }

        if (empty($_REQUEST['no_bx'])) {
            $this->user = $this->getBxInt()->get_user();
            if (is_null($this->getBxUser())) {
                throw new Exception('Invalid key.', 400);
            }
        }

        $this->user = $this->getBxInt()->get_user();
        if ($this->user['ID'] == 68393) {
//            $this->user['ID'] = 54528; // manager
//            $this->user['USER_ID'] = 54528;

//            $this->user['ID'] = 110723; // worker
//            $this->user['USER_ID'] = 110723; // worker

//            $this->user['UF_DEPARTMENT'] = [167];
        }

        $aShopRights = $this->getShopRights();
        $this->access = $this->getAccess($aShopRights);
        if (!$this->access) {
            die('У работников вашего департамента нет доступа');
        }
        $this->role = $this->getRole($aShopRights);
        $arResult = $this->call_method('ev_' . $sMethod);
        $this->jsonAnswer($arResult);
    }

    /**
     * Метод, определяющий роль пользователя (при инициализации)
     *
     * @param array $aShopRights
     * @return string роль
     */
    public function getRole(array $aShopRights): string
    {
        $sUserId = $this->user['ID'];
        if (in_array($sUserId, $this::AUDITORS_RU) || in_array(355, $this->user['UF_DEPARTMENT'])) {
            return 'Auditor';
        }
        $restBX = new BX_REST();
        foreach ($aShopRights as $key => $value) {
            $aDepInfo = $restBX->get_dep($key);
            $sDepHeadId = $aDepInfo['result'][0]['UF_HEAD'] ?? 'nof found';
            if ($sDepHeadId == $this->user['USER_ID']) {
                return 'Manager';
            }
        }
        $now = new DateTime();
        $shopRoles = DB_APP::execute("SELECT `jobid` FROM `calendar` 
                   WHERE `user_id` = ? 
                     AND `mounth` = ? 
                     AND `year` = ?", $sUserId, $now->format('m'), $now->format('Y'))->fetchAll();
        $shopRoles = array_map(function ($item) {
            return $item['jobid'];
        }, $shopRoles);
        if (in_array(4, $shopRoles)) { // Администратор магазина
            return 'Manager';
        }
        return 'Worker';
    }

    public function ev_getTsShopIdName()
    {
        $restBX = new BX_REST();
        $sUserId = json_decode(file_get_contents('php://input'), true)['userId'];
        $aUserBxData = json_decode(
            Func::getHttpData('https://crm.lichishop.com/rest/1/c2zp7066p9i7n3zf/user.get?ID=' . $sUserId),
            true)['result'];
        return self::getTsIdNameByBxId($aUserBxData[0]['UF_DEPARTMENT'][0]);
    }

    private function getTsIdNameByBxId($bitrixDepId)
    {
        $sQuery = 'SELECT * FROM department WHERE id_bx = ?';
        $aDbRes = DB_APP::execute($sQuery, $bitrixDepId)->fetch();
        return ['ts_shop_id' => $aDbRes['defaultpoint'], 'shop_name' => $aDbRes['full_name']];
    }

    /**
     * Метод, возвращающий департаменты и права департаментов текущего пользователя
     *
     * @return array [shopId => rights]
     * @throws Exception
     */
    private function getShopRights(): array
    {
        $aDepIds = $this->user['UF_DEPARTMENT'];
        $sQuery = 'SELECT id_bx, rights FROM department WHERE id_bx IN(' . implode(',', $aDepIds) . ')';
        $aDbRes = DB_APP::execute($sQuery)->fetchAll();
        $aShopRights = [];
        foreach ($aDbRes as $key => $value) {
            $aShopRights[$value['id_bx']] = $value['rights'];
        }
        return $aShopRights;
    }

    /**
     * Метод, возвращающий доступ к приложению
     *
     * @param array $aShopRights
     * @return bool доступ
     */
    private function getAccess(array $aShopRights): bool
    {
        return (in_array(3, $aShopRights) || in_array(1, $aShopRights));
    }

    public function ev_getUserData()
    {
        return ['role' => $this->role, 'user' => $this->user];
    }

    public function ev_getStores()
    {
        return $this->getStores();
    }

    private function getStores(): array
    {
        $aGetStores = Func::getHttpData(
            'http://192.168.200.251/Lichi/hs/API_V1/goodsdetails/1',
            ['namecatalog' => 'Склады'], // даты включительно
            [
                'USERPWD' => 'Web:',
                'json' => true,
                'send_json' => true,
            ]
        )['goodsdetails'];
        if (!$aGetStores) {
            die();
        }
        $aStores = [];
//        $aGetStores = \Logistic\Sync1C::getStores()['response']['goodsdetails'] ?? [];
        foreach ($aGetStores as $aShop) {
            $aShop['ИдТС'] = (int)preg_replace("/[^0-9]/", '', $aShop['ИдТС'] ?? 0);
            if ($aShop['ИдТС'] !== 0) {
                $aStores[] = [
                    'id' => $aShop['ИдТС'],
                    'name' => $aShop['Наименование']
                ];
            }
        }
        return $aStores;
    }

    public function ev_cronUpdateSalesData(): array
    {
        $sDateFrom = ((new \DateTime('now'))->modify('-2 day'))->format('Y-m-d');
        $sDateTo = date('Y-m-d');
        $this->updateVisitors($sDateFrom, $sDateTo);
        $this->updateSalesData($sDateFrom, $sDateTo);
        return ['success' => true];
    }

    /**
     * Метод для ручного обновления данных
     * @return
     */
    public function ev_updateSalesData()
    {
        $sDateFrom = '2023-01-01';
        $sDateTo = '2023-01-09';
//        return $this->updateSalesData($sDateFrom, $sDateTo);
        $this->updateSalesData($sDateFrom, $sDateTo);
        $this->updateVisitors($sDateFrom, $sDateTo);
    }

    private function updateSalesData($sDateFrom, $sDateTo)
    {
        $aSales = $this->getDataFrom1C($sDateFrom, $sDateTo);
        //$test =  $this->testCalculate($aSales);
        $aSales = $this->addTsShopId($aSales);
//        return $aSales;
        $aSales = $this->addCalendarData($aSales, $sDateFrom, $sDateTo);
        $this->preDelete($sDateFrom, $sDateTo);
        $this->saveSales($aSales);
//        return $aSales;
        $this->preDeleteVisitors($sDateFrom, $sDateTo);
        $this->updateVisitors($sDateFrom, $sDateTo);
    }

    private function getDataFrom1C(string $sDateFrom, string $sDateTo): array
    {
        $sDataFrom = date_format(date_create($sDateFrom), 'Ymd');
        $sDataTo = date_format(date_create($sDateTo), 'Ymd');
        $a1CData = Func::getHttpData(
            'http://192.168.200.251/Lichi/hs/API_KPI/DataKPI',
            ['datafrom' => $sDataFrom, 'datato' => $sDataTo], // даты включительно
            [
                'USERPWD' => 'Web:',
                'json' => true,
                'send_json' => true,
            ]
        );

        foreach ($a1CData as $iIndex => $aItem) {
            $a1CData[$iIndex] = [
                'date' => date_format(
                    date_create(substr($aItem['День'], 0, 10)),
                    'Y-m-d'),
                'shop_name' => str_replace("  ", " ", trim($aItem['Склад'])),
                'worker_name' => $aItem['Продавец'],
                'checks' => ($aItem['Чеки'] - $aItem['ДоковВозврат']) ?: 0,
                'products' => $aItem['ТоваровПродажа'] ?: 0,
                'sum' => round((int)($aItem['Сумма'])),
                'return_checks' => (int)$aItem['ДоковВозврат'],
                'return_products' => (int)$aItem['ТоваровВозврат'],
                'return_sum' =>
                    ($aItem['ВозвратБезналичные'] + $aItem['ВозвратНаличными'] +
                        $aItem['ВозвратБезналичнымиНеДеньВДень'] + $aItem['ВозвратНаличнымиНеДеньВДень']) ?: 0,
            ];
        }
        return array_values(
            array_filter($a1CData, function ($aItem) {
                return !(
                    (strpos($aItem['shop_name'], 'Склад') !== false) ||
                    (strpos($aItem['shop_name'], 'Интернет') !== false) ||
                    (strpos($aItem['worker_name'], 'Реклама') !== false) ||
                    (strpos($aItem['worker_name'], 'Касса') !== false) ||
                    (empty($aItem['shop_name']))
                );
            })
        );
    }

    private function addTsShopId(array $aSales): array
    {
        $aShops1C = $this->getStores();
        $aShops = [];
        foreach ($aShops1C as $aShop) {
            $aShops[trim($aShop['name'])] = $aShop['id'];
        }
        foreach ($aSales as $iIndex => $aShopData) {
            $aSales[$iIndex]['ts_shop_id'] = $aShops[$aShopData['shop_name']] ?? 0;
        }
        return $aSales;
    }

    private function addCalendarData(array $aSales, string $sDateFrom, string $sDateTo): array
    {
        $aCalendarData = $this->getCalendarData($sDateFrom, $sDateTo);
        foreach ($aSales as $iIndex => $aSale) {
            $aCalendarRow = array_values(
                array_filter($aCalendarData, function ($aRow) use ($aSale) {
                    $sSaleYear = date_format(date_create($aSale['date']), 'Y');
                    $sSaleMonth = date_format(date_create($aSale['date']), 'n');
                    $sSaleDay = date_format(date_create($aSale['date']), 'j');
                    if (!empty($aSale['worker_name'])) {
                        return ($aRow['name'] == $aSale['worker_name'] || (
                                    (strpos($aRow['name'], explode(' ', $aSale['worker_name'])[0]) !== false)
                                    &&
                                    (strpos($aRow['name'], explode(' ', $aSale['worker_name'])[1]) !== false)
                                ))
                            && $aRow['shopid'] == $aSale['ts_shop_id'] &&
                            $aRow['year'] == $sSaleYear && $aRow['mounth'] == $sSaleMonth && !empty($aRow[$sSaleDay]);
                    } else {
                        return false;
                    }
                })
            );
            $aCalendarRows = array_values(
                array_filter($aCalendarData, function ($aRow) use ($aSale) {
                    if (!empty($aSale['worker_name'])) {
                        return ($aRow['name'] == $aSale['worker_name'] || (
                                    (strpos($aRow['name'], explode(' ', $aSale['worker_name'])[0]) !== false)
                                    &&
                                    (strpos($aRow['name'], explode(' ', $aSale['worker_name'])[1]) !== false)
                                ))
                            && $aRow['shopid'] == $aSale['ts_shop_id'];
                    } else {
                        return false;
                    }
                })
            );
            if (!empty($aCalendarRow) && (count($aCalendarRow) == 1)) {
                $aSales[$iIndex]['user_id'] = $aCalendarRow[0]['user_id'];
                $aSales[$iIndex]['job_id'] = $aCalendarRow[0]['jobid'];
                $aSales[$iIndex]['position_user'] = self::JOB_ID_POSITION[$aSales[$iIndex]['job_id']] ?? '';
                $aSales[$iIndex]['worked_hours'] = $aCalendarRow[0][date_format(date_create($aSale['date']), 'j')];
                $iPlanHours = 0;
                foreach ($aCalendarRow[0] as $key => $value) {
                    if (is_numeric($key) && !empty($value)) {
                        $iPlanHours += $value;
                    }
                }
                $aSales[$iIndex]['plan_hours'] = $iPlanHours;
                $aSales[$iIndex]['all_days'] = 1;
            } else {
                $aSales[$iIndex]['user_id'] = $aCalendarRows[0]['user_id'] ?? 0;
                $aSales[$iIndex]['job_id'] = $aCalendarRows[0]['jobid'] ?? 0;
                $aSales[$iIndex]['position_user'] = 0;
                $aSales[$iIndex]['position_user'] = self::JOB_ID_POSITION[$aSales[$iIndex]['job_id']] ?? '';
                $aSales[$iIndex]['worked_hours'] = 0;
                $aSales[$iIndex]['plan_hours'] = 0;
                $aSales[$iIndex]['all_days'] = 0;
            }
        }

        return $aSales;
    }

    private function getCalendarData(string $sDateFrom, string $sDateTo)
    {
        $sMonthFrom = date_format(date_create($sDateFrom), 'n');
        $sYearFrom = date_format(date_create($sDateFrom), 'Y');
        $sMonthTo = date_format(date_create($sDateTo), 'n');
        if ($sMonthFrom == 1) {
            $sMonthFrom = 12;
            $sYearFrom = $sYearFrom - 1;
        } else {
            $sMonthFrom -= 1;
        }
        $sYearTo = date_format(date_create($sDateTo), 'Y');
        $aDbRes = DB_APP::execute(
            'SELECT * FROM calendar WHERE (mounth = ? AND `year` = ?) OR (mounth = ? AND `year` = ?)',
            $sMonthFrom, $sYearFrom, $sMonthTo, $sYearTo
        )->fetchAll();
        return $aDbRes;
    }

    private function saveSales($aSales)
    {
        $sQueryData = implode(',',
            array_map(function ($aSale) {
                return '(' .
                    0 . ',' .
                    '"' . $aSale['date'] . '",' .
                    $aSale['ts_shop_id'] . ',' .
                    '"' . $aSale['shop_name'] . '" ,' .
                    $aSale['user_id'] . ',' .
                    '"' . $aSale['worker_name'] . '" ,' .
                    $aSale['job_id'] . ',' .
                    $aSale['sum'] . ',' .
                    $aSale['checks'] . ',' .
                    $aSale['products'] . ',' .
                    $aSale['return_sum'] . ',' .
                    $aSale['return_checks'] . ',' .
                    $aSale['return_products'] .
                    ')';
            }, $aSales)
        );
        DB_APP::execute('INSERT INTO kpi_sales VALUES ' . $sQueryData);
    }

    /**
     * Обертка для возвращения продаж по магазинам
     * @return array|array[]
     */
    public function ev_getShops()
    {
        $aInput = json_decode(file_get_contents('php://input'), true);
        $sDateFrom = $aInput[0];
        $sDateTo = $aInput[1];
        return array_map(function ($aItem) {
            return [
                'id_shop' => $aItem['shop_id'],
                'name_shop' => $aItem['shop_name'],
                'plan_sum' => $aItem['sales_plan'],
                'fact_sum' => $aItem['sales_sum'],
                'fact_percent' => $aItem['sales_fact_percent'],
                'return_sum' => $aItem['return_sum'],
                'products_units' => $aItem['sales_products'],
                'checks_units' => $aItem['sales_checks'],
                'UPT' => $aItem['UPT'],
                'check_average' => $aItem['avg_check'],
                'visitors' => $aItem['visitors'],
                'conversion' => $aItem['conversion'],
                'country' => $aItem['country'],

            ];
        }, $this->getShopsSales($sDateFrom, $sDateTo));
    }

    /**
     * Продажи по магазинам
     * @param $sDateFrom
     * @param $sDateTo
     * @return mixed
     */
    public function getShopsSales($sDateFrom, $sDateTo)
    {
        // TODO: итерирование по месяцам

        $aSales = DB_APP::execute(
            'SELECT `shop_id`, d.country as country, `shop_name`, 
                   SUM(sales_sum) as sales_sum, SUM(sales_checks) as sales_checks, SUM(sales_products) as sales_products, 
                   SUM(return_sum) as return_sum, SUM(return_checks) as return_checks, SUM(return_products) as return_products
            FROM kpi_sales 
                LEFT JOIN (
                    SELECT DISTINCT department.defaultpoint as point, department.country as country FROM department) as d
                	ON d.point = shop_id
            WHERE `date` BETWEEN ? AND ?
            GROUP BY shop_id',
            $sDateFrom, $sDateTo
        )->fetchAll();
        $aDateFrom = getdate(strtotime($sDateFrom));
        $aDateTo = getdate(strtotime($sDateTo));

        $aDbPlans = DB_APP::execute('SELECT shopid, plan, qty FROM calendar_edit WHERE `mounth` = ? AND `year` = ?',
            $aDateFrom['mon'], $aDateFrom['year']
        )->fetchAll();
        $aPlans = [];
        foreach ($aDbPlans as $aPlan) {
            $aPlans[$aPlan['shopid']] = ['plan' => $aPlan['plan'], 'qty' => $aPlan['qty']];
        }

        $aVisitors = DB_APP::execute('
            SELECT shop_id, SUM(entered) as entered, SUM(went_out) as went_out 
            FROM kpi_visitors 
            WHERE `date` BETWEEN ? AND ? 
            GROUP BY shop_id',
            $sDateFrom, $sDateTo
        )->fetchAll();
        $aVisitors = array_combine(
            array_map(function ($aItem) {
                return $aItem['shop_id'];
            }, $aVisitors),
            array_map(function ($aItem) {
                return ['entered' => $aItem['entered'], 'went_out' => $aItem['went_out']];
            }, $aVisitors)
        );

        foreach ($aSales as &$aSale) {
            if (!empty($aPlans[$aSale['shop_id']])) {
                $aSale['sales_plan'] = $aPlans[$aSale['shop_id']]['plan'];
                $aSale['sales_fact_percent'] = $aSale['sales_plan'] == 0 ? 0 :
                    round($aSale['sales_sum'] / $aSale['sales_plan'] * 100, 1);
                $aSale['sales_forecast_percent'] = $aSale['sales_plan'] == 0 ? 0 :
                    round($aSale['sales_sum'] / $aSale['sales_plan'] * 100
                        / $aDateTo['mday'] * cal_days_in_month(CAL_GREGORIAN, $aDateTo['mon'], $aDateTo['year']),
                        1);
            } else {
                $aSale['sales_plan'] = 0;
                $aSale['sales_fact_percent'] = 0;
                $aSale['sales_forecast_percent'] = 0;
            }
            if (isset($aVisitors[$aSale['shop_id']])) {
                if (in_array($aSale['shop_id'], $this::COUNT_VISITORS_RULE['entered'])) {
                    $iVisitors = $aVisitors[$aSale['shop_id']]['entered'];
                } elseif (in_array($aSale['shop_id'], $this::COUNT_VISITORS_RULE['went_out'])) {
                    $iVisitors = $aVisitors[$aSale['shop_id']]['went_out'];
                } else {
                    $iVisitors = max($aVisitors[$aSale['shop_id']]['entered'], $aVisitors[$aSale['shop_id']]['went_out']);
                }
            } else {
                $iVisitors = 0;
            }
            $aSale['visitors'] = $iVisitors;

//            $aSale['visitors'] = isset($aVisitors[$aSale['shop_id']]) ?
//                max($aVisitors[$aSale['shop_id']]['entered'], $aVisitors[$aSale['shop_id']]['went_out']) : 0;


            $aSale['conversion'] = $aSale['sales_checks'] ?
                round($aSale['visitors'] / $aSale['sales_checks'] * 100, 1) : 0;

            $aSale['UPT'] = $aSale['sales_checks'] == 0 ? 0 :
                round($aSale['sales_products'] / $aSale['sales_checks'], 2);
            $aSale['avg_check'] = $aSale['sales_checks'] == 0 ? 0 :
                round(($aSale['sales_sum'] + $aSale['return_sum']) / $aSale['sales_checks']);
        }
        usort($aSales, function ($item1, $item2) {
            return $item1['shop_name'] <=> $item2['shop_name'];
        });
        return $aSales;
    }

    /**
     * Обертка продажи одного магазина по работникам
     * @return array|array[]
     */
    public function ev_getSales()
    {
        $aInput = json_decode(file_get_contents('php://input'), true);
//        return $aInput;
        $sShopId = $aInput[0]['id'];
        $sDateFrom = $aInput[1]['date_from'];
        $sDateTo = $aInput[2]['date_to'];
        $aSales = array_values(array_filter($this->getUsersSales($sDateFrom, $sDateTo), function ($aItem) use ($sShopId) {
            return $aItem['shop_id'] == $sShopId;
        }));
        return array_map(function ($aItem) {
            return [
                'id_shop' => $aItem['shop_id'],
                'id_user' => $aItem['user_id'],
                'name_user' => $aItem['user_name'],
                'position_user' => self::JOB_ID_POSITION[$aItem['user_position']],
                'worked_hours' => $aItem['worked_hours'],
                'plan_hours' => $aItem['plan_hours'],
                'plan_sum' => $aItem['sales_plan'],
                'fact_sum' => $aItem['sales_sum'],
                'fact_percent' => $aItem['sales_fact_percent'],
                'forecast_percent' => $aItem['sales_forecast_percent'],
                'return_sum' => $aItem['return_sum'],
                'return_checks' => $aItem['return_checks'],
                'return_products' => $aItem['return_products'],
                'products_units' => $aItem['sales_products'],
                'checks_units' => $aItem['sales_checks'],
                'UPT' => $aItem['UPT'],
                'check_average' => $aItem['avg_check']
            ];
        }, $aSales);

    }

    /**
     * Продажи работников
     * @param $sDateFrom
     * @param $sDateTo
     * @return mixed
     */
    public function getUsersSales($sDateFrom, $sDateTo)
    {
        $aSales = DB_APP::execute(
            'SELECT `shop_id`, shop_name, 
                    user_id, user_name, user_position,
                    SUM(sales_sum) as sales_sum, SUM(sales_checks) as sales_checks, SUM(sales_products) as sales_products, 
                    SUM(return_sum) as return_sum, SUM(return_checks) as return_checks, SUM(return_products) as return_products
            FROM kpi_sales
            WHERE `date` BETWEEN ? AND ?
            GROUP BY user_id, shop_id',
            $sDateFrom, $sDateTo
        )->fetchAll();
        $aDateFrom = getdate(strtotime($sDateFrom));
        $aDateTo = getdate(strtotime($sDateTo));
//        if ($aDateFrom['mday'] == 1 && $aDateFrom['mon'] == $aDateTo['mon']) {
        if ($aDateFrom['mon'] == $aDateTo['mon']) {
            $aDbPlans = DB_APP::execute('SELECT shopid, plan, qty FROM calendar_edit WHERE `mounth` = ? AND `year` = ?',
                $aDateFrom['mon'], $aDateFrom['year']
            )->fetchAll();
            $aPlans = [];
            foreach ($aDbPlans as $aPlan) {
                $aPlans[$aPlan['shopid']] = ['plan' => $aPlan['plan'], 'qty' => $aPlan['qty']];
            }
        }

        $aShopsWithAdmins = DB_APP::execute(
            'SELECT shopid as shop_id, COUNT(*) as workers 
            FROM calendar 
            WHERE shopid IN (
                SELECT DISTINCT shopid 
                    FROM calendar 
                WHERE `mounth` = ? AND `year` = ? AND jobid = 4)
            AND jobid = 1 AND `mounth` = ? AND `year` = ?
            GROUP BY shopid',
            $aDateFrom['mon'], $aDateFrom['year'], $aDateFrom['mon'], $aDateFrom['year']
        )->fetchAll();

        $aShopsWithAdmins = array_combine(
            array_map(function ($aItem) {
                return $aItem['shop_id'];
            }, $aShopsWithAdmins),
            array_map(function ($aItem) {
                return $aItem['workers'];
            }, $aShopsWithAdmins)
        );

        $aCalendarTime = DB_APP::execute(
            'SELECT * FROM calendar WHERE `mounth` = ? AND `year` = ?',
            $aDateFrom['mon'], $aDateFrom['year']
        )->fetchAll();
        $aCalendarTime = array_combine(
            array_map(function ($aItem) {
                return $aItem['user_id'];
            }, $aCalendarTime),
            array_map(function ($aItem) use ($aDateFrom, $aDateTo) {
                $iWorkedHours = 0;
                $iPlanHours = 0;
                foreach ($aItem as $key => $value) {
                    if (is_numeric($key) && !empty($value)) {
                        $iPlanHours += (int)$value;
                        if ($key >= $aDateFrom['mday'] && $key <= $aDateTo['mday']) {
                            $iWorkedHours += $value;
                        }
                    }
                }
                return ['worked_hours' => $iWorkedHours, 'plan_hours' => $iPlanHours];
            }, $aCalendarTime)
        );
//        print_r($aShopsWithAdmins);
//        print_r($aPlans);
//        die();
        foreach ($aSales as &$aSale) {
            if (!empty($aPlans[$aSale['shop_id']])) {
                $aSale['calendar_edit_data'] = $aPlans[$aSale['shop_id']];
                if (in_array($aSale['shop_id'], array_keys($aShopsWithAdmins))) {
                    if ($aSale['user_position'] == 1) {
                        $aSale['sales_plan'] =
//                            round($aPlans[$aSale['shop_id']]['plan'] / $aShopsWithAdmins[$aSale['shop_id']]);
                            round($aPlans[$aSale['shop_id']]['plan'] / ($aPlans[$aSale['shop_id']]['qty'] ?: 1));

                    } else {
                        $aSale['sales_plan'] = 0;
                    }
                } else {
                    if ($aSale['user_position'] == 1) {
                        $aSale['sales_plan'] = round($aPlans[$aSale['shop_id']]['plan'] / ($aPlans[$aSale['shop_id']]['qty'] - 0.5));
                    } else if ($aSale['user_position'] == 2) {
                        $aSale['sales_plan'] = round((
                                $aPlans[$aSale['shop_id']]['plan'] -
                                ($aPlans[$aSale['shop_id']]['plan'] / ((int)$aPlans[$aSale['shop_id']]['qty'] - 0.5)) *
                                ((int)$aPlans[$aSale['shop_id']]['qty'] - 2)
                            ) / 2);
                    } else if ($aSale['user_position'] == 3 || $aSale['user_position'] == 4) {
                        $aSale['sales_plan'] = round($aPlans[$aSale['shop_id']]['plan']);
                    } else {
                        $aSale['sales_plan'] = 0;
                    }
                }
                $aSale['sales_fact_percent'] = $aSale['sales_plan'] == 0 ? 0 :
                    round($aSale['sales_sum'] / $aSale['sales_plan'] * 100, 1);
                $aSale['sales_forecast_percent'] = $aSale['sales_plan'] == 0 ? 0 :
                    round($aSale['sales_sum'] / $aSale['sales_plan'] * 100
                        / $aDateTo['mday'] * cal_days_in_month(CAL_GREGORIAN, $aDateTo['mon'], $aDateTo['year']),
                        1);
            } else {
                $aSale['sales_plan'] = 0;
                $aSale['sales_fact_percent'] = 0;
                $aSale['sales_forecast_percent'] = 0;
            }
            if (!empty($aCalendarTime[$aSale['user_id']])) {
                $aSale = array_merge($aSale, $aCalendarTime[$aSale['user_id']]);
            } else {
                $aSale['worked_hours'] = 0;
                $aSale['plan_hours'] = 0;
            }
            $aSale['UPT'] = $aSale['sales_checks'] == 0 ? 0 :
                round($aSale['sales_products'] / $aSale['sales_checks'], 2);
            $aSale['avg_check'] = $aSale['sales_checks'] == 0 ? 0 :
                round(($aSale['sales_sum'] + $aSale['return_sum']) / $aSale['sales_checks']);
        }
        return $aSales;

    }

    /**
     * Метод вместо INSERT OR UPDATE
     * @param $sDateFrom
     * @param $sDateTo
     */
    private function preDelete($sDateFrom, $sDateTo)
    {
        DB_APP::execute('DELETE FROM kpi_sales WHERE `date` BETWEEN ? AND ?', $sDateFrom, $sDateTo);
    }

    /**
     * Метод вместо INSERT OR UPDATE
     * @param $sDateFrom
     * @param $sDateTo
     */
    private function preDeleteVisitors($sDateFrom, $sDateTo)
    {
        DB_APP::execute('DELETE FROM kpi_visitors WHERE `date` BETWEEN ? AND ?', $sDateFrom, $sDateTo);
    }

    public function updateVisitors(string $sDateFrom, string $sDateTo)
    {
        $oPeriod = new DatePeriod(
            new DateTime($sDateFrom),
            new DateInterval('P1D'),
            new DateTime($sDateTo . ' 23:59')
        );
        $this->preDeleteVisitors($sDateFrom, $sDateTo);
        foreach ($oPeriod as $oDate) {
            $this->updateVisitorsDate(
                $oDate->format('Y-m-d')
            );
        }
    }

    private function updateVisitorsDate($sDate)
    {
        $aShopPath = [
            '28795' => '/Lichi/rossiya/leningradskaya_obl/sankt-peterburg/galereya_ligovsk_30a/2D/0101_',
            '153682' => '/Lichi/rossiya/moskva/moskva/atrium_zemlyanoyval_33/vkhod/0101_',
            '24375' => '/Lichi/rossiya/moskva/moskva/afimoll_presnenskaya_nab._2/vkhod/0101_',
            '28792' => '/Lichi/rossiya/moskva/moskva/metropolis_leningradskoe_16a/vkhod/0102_',
            '44371' => '/Lichi/rossiya/moskva/moskva/kolumbus_kirovograds_kaya_13a/vkhod/0101_',
            '21786' => '/Lichi/rossiya/moskva/moskva/vavilon_pr._mira_211a/vkhod/0101_',
            '21788' => '/Lichi/rossiya/leningradskaya_obl/sankt-peterburg/evropol_polyustr_84/vkhod/0101_',
            '64295' => '/Lichi/rossiya/sverdlovskaya_o./ekaterinburg/grinvich_8_marta_0/vkhod/0101_',
            '76083' => '/Lichi/rossiya/krasnodarskiy_k/sochi/moremoll_0_0/magazin/0101_',
            '135027' => '/Lichi/rossiya/krasnodarskiy_k/krasnodar/galereya_golovatogo_313/vkhod/0101_',
            '165954' => '/Lichi/rossiya/rostovskaya_obl/rostov-na-donu/gorizont_omskaya_2b/vkhod/0101_',
            '99998' => '/Lichi/rossiya/kazan/kazan/trts_mega_1_1/trts_mega/0101_',
            '99999' => '/Lichi/rossiya/nizhegorodskaya_o/nizhniy_novgorod/fantastika_1_1/vkhod/0101_',
            '88889' => '/Lichi/rossiya/moskva/moskva/38._moskva_evropeyskiy/vkhod/0101_',

            '3' => '/Lichi/kazakhstan/Karaganda/Karaganda/CityMoll_1_1/1/0101_',
            '10183' => '/Lichi/kazakhstan/Aktobe/Aktobe/Keruen_City_ul._mametovoy_4/Lichi/0102_',
            '10434' => '/Lichi/kazakhstan/Almaty_/Almaty/MEGA_1_2/MEGA/0101_',
            '20521' => '/Lichi/kazakhstan/Almaty_/Almaty/Megapark_1_1/1/0101_',
            '17726' => '/Lichi/kazakhstan/Atyrau/Atyrau/baizar_111_11111/1/0101_',
            '20784' => '/Lichi/kazakhstan/Kostanay/Kostanay/Plaza_pr.Nazarbayeva__183/23-28/0101_',
            '18841' => '/Lichi/kazakhstan/Nursultan/Nursultan/khan-shatyr_prospekt_turan__37/b-258/0101_',
            '252689' => '/Lichi/kazakhstan/Shymkent/Shymkent/MEGA_Shymkent_Shymkent/MEGA/0101_',
            '18842' => '/Lichi/kazakhstan/Nursultan/Nursultan/Sikway_kabanbay_batyra_62/Lichi/0101_'
        ];
        $sFilename = date('Y-m-d', strtotime($sDate));
        $aRes = [];
        foreach ($aShopPath as $sShopId => $sPath) {
            $iCountIn = 0;
            $iCountOut = 0;
            if ($sPath) {
                $file = fopen('ftp://1cbit:WPuq5qwT74Vgt3Yh@network-1.lichishop.com:10000' . $sPath . $sFilename, 'r');
                if ($file) {
                    while (($sRow = fgets($file)) !== false) {
                        $aRow = explode(' ', $sRow);
                        if (count($aRow) == 3) {
                            $iCountIn += (int)$aRow[1];
                            $iCountOut += (int)$aRow[2];
                        }
                    }
                }
                $aRes[$sShopId] = ['path' => $sPath, 'in' => $iCountIn, 'out' => $iCountOut];
            }
        }
        $sQueryString = implode(',', array_map(function ($sShopId, $aItem) use ($sDate) {
            return '(' . 0 . ',' .
                '"' . $sDate . '",' .
                $sShopId . ',' .
                $aItem['in'] . ',' .
                $aItem['out'] .
                ')';
        }, array_keys($aRes), $aRes));

//        print_r('INSERT INTO kpi_visitors VALUES ' . $sQueryString);

        DB_APP::execute('INSERT INTO kpi_visitors VALUES ' . $sQueryString);
//        print_r($aRes);
    }


    private function getMonthsBetweenDates(string $sDateFrom, string $sDateTo)
    {
        $oPeriod = new DatePeriod(
            new DateTime($sDateFrom),
            new DateInterval('P1D'),
            new DateTime($sDateTo . ' 23:59')
        );
        $aDates = [];
        $oPrevDate = '';
        $aPrevDate = [];
        $aCurrentTuple = [];

        foreach ($oPeriod as $oDate) {
            $aDate = getdate(date_timestamp_get($oDate));
            if (empty($aPrevDate)) {
                $aPrevDate = $aDate;
                $oPrevDate = $oDate;
                $aCurrentTuple[0] = $oDate->format('Y-m-d');
                continue;
            }
            if ($aPrevDate['mon'] != $aDate['mon']) {
                $aCurrentTuple[1] = $oPrevDate->format('Y-m-d');
                $aDates[] = $aCurrentTuple;
                $aCurrentTuple = [0 => $oDate->format('Y-m-d')];
            }
            if ($oDate->format('Y-m-d') == $sDateTo) {
                $aCurrentTuple[1] = $oDate->format('Y-m-d');
                $aDates[] = $aCurrentTuple;
            }
            $aPrevDate = $aDate;
            $oPrevDate = $oDate;
        }
        return $aDates;
    }

    public function ev_test()
    {
//        return $this->updateVisitorsDate('2022-10-07');
        $sDateFrom = '2022-11-01';
        $sDateTo = '2022-11-15';
        $aSales = $this->getDataFrom1C($sDateFrom, $sDateTo);
        return $aSales;

        return [
            'calculate' => $this->testCalculate($aSales),
//            '1CData' => array_filter($aSales, function($aItem) {
//                return $aItem['shop_name'] == '33. Москва Европолис';
//            })
        ];
    }

    private function testCalculate($aSales)
    {
        $aRes = [];
        foreach ($aSales as $aSale) {

//            return $aSale;
            if (isset($aRes[$aSale['shop_name']])) {
                $aRes[$aSale['shop_name']] += $aSale['products'];
//                $aRes[$aSale['shop_name']]['products'] += $aSale['products'];
//                $aRes[$aSale['shop_name']] += $aSale['sum'];

//                $aRes[$aSale['shop_name']][$aSale['date']]['items'] = [$aSale];
            } else {
                $aRes[$aSale['shop_name']] = $aSale['products'];
//                $aRes[$aSale['shop_name']]['products'] += $aSale['products'];
//                $aRes[$aSale['shop_name']] = $aSale['sum'];

//                $aRes[$aSale['shop_name']][$aSale['date']]['items'][] = $aSale;
            }
//            if (isset($aRes[$aSale['shop_name']][$aSale['date']])) {
//                $aRes[$aSale['shop_name']][$aSale['date']]['sum'] += $aSale['sum'];
//                $aRes[$aSale['shop_name']][$aSale['date']]['items'] = [$aSale];
//            } else {
//                $aRes[$aSale['shop_name']][$aSale['date']]['sum'] = $aSale['sum'];
//                $aRes[$aSale['shop_name']][$aSale['date']]['items'][] = $aSale;
//            }
        }
        return $aRes;
    }

}
