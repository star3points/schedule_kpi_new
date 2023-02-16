<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->group(['prefix' => '/api'], function ($router) {
    $router->group(['prefix' => '/schedule'], function ($router) {
        $router->get('upsert_old_calendar', ['uses' => 'ScheduleController@upsertDataOldCalendar']);
    });
    $router->group(['prefix' => '/kpi'], function ($router) {
        $router->get('update_sales', ['uses' => 'KpiController@updateSales']);
    });
});

// TODO: check_ability.{} => can.{}
$router->group(['prefix' => '/app'], function ($router) {
    $router->group(['prefix' => '/schedule'], function($router) {
        $router->get('get_role', ['uses' => 'ScheduleController@getRole']);
        $router->get('get_shop_list', ['uses' => 'ScheduleController@getShopList']);
        $router->get('get_role_list', ['uses' => 'ScheduleController@getRoleList']);
        $router->get('search_bitrix_user', ['uses' => 'ScheduleController@searchUser']);
        $router->get('get_schedule', [
//            'middleware' => 'check_ability:schedule_get_schedule',
            'uses' => 'ScheduleController@getSchedule'
        ]);
        $router->get('get_month_data', [
//            'middleware' => 'check_ability:schedule_get_month_data',
            'uses' => 'ScheduleController@getMonthData'
        ]);
        $router->post('edit_worker', [
//            'middleware' => 'check_ability:schedule_edit_worker',
            'uses' => 'ScheduleController@editWorker'
        ]);
        $router->post('update_month_data', [
//            'middleware' => 'check_ability:schedule_update_month_data',
            'uses' => 'ScheduleController@updateComment'
        ]);
    });
    $router->group(['prefix' => '/kpi'], function ($router) {
        $router->get('get_role', ['uses' => 'KpiController@getRole']);
        $router->get('get_shops', [
            'middleware' => 'check_ability:kpi_shops',
            'uses' => 'KpiController@getShops'
        ]);
        $router->get('get_shop', [
            'middleware' => 'check_ability:kpi_shop',
            'uses' => 'KpiController@getShop'
        ]);
        $router->get('get_worker', [
            'middleware' => 'check_ability:kpi_worker',
            'uses' => 'KpiController@getWorker'
        ]);
    });
});

$router->post('/{route:.*}/', function ()  {
    return view('app');
});

$router->get('/{route:.*}/', function ()  {
    return view('app');
});
