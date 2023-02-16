<?php

namespace App\Providers;

use App\Repositories\KpiRepository;
use App\Services\ExternalApi\Bitrix\BitrixUser;
use App\Services\ExternalApi\Bitrix\BitrixUserTest;
use App\Services\ExternalApi\Bitrix\BitrixUserInterface;
use App\Services\KpiService;
use App\Services\TableConstructor\AntdTableConstructor;
use App\Services\TableConstructor\TableConstructorInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $bindings = [
//        TableConstructorInterface::class => AntdTableConstructor::class,
        KpiService::class,
        KpiRepository::class,
        BitrixUser::class
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TableConstructorInterface::class, function ($app) {
            return new AntdTableConstructor();
        });
        $this->app->bind(BitrixUserInterface::class, function ($app) {
//            return new BitrixUser();
            return new BitrixUserTest();
        });
    }
}
